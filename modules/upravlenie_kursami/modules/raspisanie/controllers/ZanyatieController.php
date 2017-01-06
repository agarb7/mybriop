<?php
namespace app\upravlenie_kursami\raspisanie\controllers;

use app\enums2\StatusRaspisaniyaKursa;
use app\modules\upravlenie_kursami\modules\raspisanie\widgets\PrepodavatelPeresechenieContent;
use app\records\Auditoriya;
use app\records\Tema;
use app\records\ZanyatieChastiTemy;
use app\upravlenie_kursami\models\FizLico;
use app\upravlenie_kursami\raspisanie\models\Kurs;
use kartik\mpdf\Pdf;
use Yii;

use yii\base\ErrorException;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;

use app\enums2\Rol;

use app\upravlenie_kursami\raspisanie\models\Zanyatie;
use app\upravlenie_kursami\raspisanie\models\KursForm;
use app\upravlenie_kursami\raspisanie\data\DayData;

/**
 * Class ZanyatieController
 */
class ZanyatieController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Rol::RUKOVODITEL_KURSOV]
                    ]
                ],
            ]
        ];
    }

    /**
     * Index action
     *
     * @param integer $kurs
     * @return string
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionIndex($kurs)
    {
        $kursForm = $this->findKursForm($kurs);
        $kursForm->ensureRaspisanieDates();

        $kursRecord = clone $kursForm;

        if ($kursForm->load(Yii::$app->request->post()) && $kursForm->save()) {
            $kursRecord = $kursForm;
        }

        $gridData = new DayData(['kurs' => $kursRecord]);

        $prepodavateli = ArrayHelper::merge(['' => ''], FizLico::findPrepodavateli()
            ->select('fiz_lico.id, familiya, imya, otchestvo')
            ->orderBy('familiya, imya, otchestvo')
            ->listItems(function ($fizLico) {
                return Yii::$app->formatter->asFizLico($fizLico);
            }));

        $auditorii = ArrayHelper::merge(['' => ''], Auditoriya::find()->listItems());

        $user = Yii::$app->user->identity;

        return $this->render('index', [
            'gridData' => $gridData,
            'kursForm' => $kursForm,
            'kursRecord' => $kursRecord,
            'auditorii' => $auditorii,
            'prepodavateli' => $prepodavateli,
            'user' => $user,
        ]);
    }

    /**
     * sign raspisanie
     */
    public function actionSignRaspisanie()
    {
        $kurs = null;
        if (isset($_GET['kurs'])){
            $kurs = $_GET['kurs'];
        }
        else{
            throw new \HttpRequestException('kurs get parameter is required');
        }
        $kursRecord = Kurs::findOne($kurs);
        if (!$kursRecord){
            throw new ErrorException('kurs wirh id '. $kurs . ' doesn`t exist');
        }
        $kursRecord->status_raspisaniya = StatusRaspisaniyaKursa::ZAVERSHENO;
        if ($kursRecord->save()){
            return $this->redirect('/upravlenie-kursami/raspisanie/zanyatie?kurs='.$kursRecord->id);
        }
        else{
            throw new ErrorException('Save error! Data wasn`t updated');
        }

    }

    /**
     * unsign raspisanie
     */
    public function actionUnsignRaspisanie()
    {
        $kurs = null;
        if (isset($_GET['kurs'])){
            $kurs = $_GET['kurs'];
        }
        else{
            throw new \HttpRequestException('kurs get parameter is required');
        }
        $kursRecord = Kurs::findOne($kurs);
        if (!$kursRecord){
            throw new ErrorException('kurs wirh id '. $kurs . ' doesn`t exist');
        }
        $kursRecord->status_raspisaniya = StatusRaspisaniyaKursa::REDAKTIRUETSYA;
        if ($kursRecord->save()){
            return $this->redirect('/upravlenie-kursami/raspisanie/zanyatie?kurs='.$kursRecord->id);
        }
        else{
            throw new ErrorException('Save error! Data wasn`t updated');
        }

    }

    public function actionSendToUo(){
        $kurs = null;
        if (isset($_GET['kurs'])){
            $kurs = $_GET['kurs'];
        }
        else{
            throw new \HttpRequestException('kurs get parameter is required');
        }
        $kursRecord = Kurs::findOne($kurs);
        if (!$kursRecord){
            throw new ErrorException('kurs wirh id '. $kurs . ' doesn`t exist');
        }
        $kursRecord->data_otpravki_v_uo = date('m/d/Y h:i:s a', time());

        $kursRecord->save();

        $sql = 'SELECT * FROM fiz_lico
                WHERE id IN (
                  SELECT fiz_lico
                  FROM polzovatel
                  WHERE \'uch_otd\' = ANY (roli)
                ) and fiz_lico.email IS NOT NULL';

        $sotrudnikEmails = FizLico::findBySql($sql)->all();
        foreach ($sotrudnikEmails as $sotrudnikEmail) {
            \Yii::$app->mailer->compose('/email/v-uo.php',[
                'sotrudnik' => $sotrudnikEmail,
                'kurs' => $kursRecord
            ])
                ->setSubject('Расписание курса "'.$kursRecord->nazvanie.'" готово к проверке')
                ->setTo($sotrudnikEmail->email)
                ->send();
        }
        $_SESSION['success_msg'] = 'Курс успешно отправлен в учебныйотдел';
        return $this->redirect('/upravlenie-kursami/raspisanie/zanyatie?kurs='.$kursRecord->id);

    }

    private function getPdfSeetings($content, $title = ''){
        $result = [];
        $indent = 3;
        $css = '
                body{
                   font-family:"Times New Roman","serif";
                }
                .paragraph{
                    text-align:justify;
                    margin-bottom: 5px;
                    margin-top: 5px;
                }
                .center{
                 text-align:center;
                }
                .tb {border-collapse: collapse}
                .tb td {padding: 5px;border: 1px solid #000}
                .inline-block{
                    display: inline-block;
                }
                .indent{padding-left: ' . $indent . 'em}

                .double-indent{padding-left: ' . (2 * $indent) . 'em}

                .indent-block{
                    margin-left: ' . $indent . 'em;
                }
                .bold{
                    font-weight: bold;
                }
                ';

        $result = [
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => $css,
            // set mPDF properties on the fly
            'options' => ['title' => $title],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetFooter' => [''],
            ]
        ];
        return $result;
    }

    public function actionPrintRaspisanie(){
        if (!isset($_GET['kurs'])){
            throw new ErrorException('Kurs is required');
        }
        $kurs_id = $_GET['kurs'];
        $schedule = [];
        $sql = 'select
                    z.data,
                    z.nomer,
                    t.nazvanie as tema,
                    z.forma,
                    fl.familiya,
                    fl.imya,
                    fl.otchestvo,
                    a.nazvanie as auditoriya,
                    rpt.nazvanie as tip_rabot
                from zanyatie as z
                inner join zanyatie_chasti_temy as zct on z.id = zct.zanyatie
                inner join tema as t on zct.tema = t.id
                inner join podrazdel_kursa as pk on t.podrazdel = pk.id
                inner join razdel_kursa as rk on pk.razdel = rk.id
                inner join auditoriya as a on z.auditoriya = a.id
                inner join fiz_lico as fl on z.prepodavatel = fl.id
                inner join rabota_po_teme as rpt on t.tip_raboty = rpt.id
                where rk.kurs = :kurs_id
                order by z.data, z.nomer';
        $data = Yii::$app->db->createCommand($sql)
                                 ->bindValue(':kurs_id', $kurs_id)
                                 ->queryAll();
        foreach ($data as $item){
            if (!isset($schedule[$item['data']])){
                $schedule[$item['data']] = [];
            }
            $schedule[$item['data']][] = $item;
        }
        $kurs = \app\records\Kurs::findOne($kurs_id);
        $content =  $this->renderPartial('/pdf/raspisanie.php', [
            'kurs' => $kurs,
            'schedule' => $schedule
        ]);

        $pdf = new Pdf($this->getPdfSeetings($content, 'Расписание курса "'.$kurs->nazvanie.'"'));
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    /**
     * Update action for AJAX
     * 
     * @param integer $kurs
     * @param integer $data
     * @param integer $nomer
     * @return array|bool
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($kurs, $data, $nomer)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $kursForm = $this->findKursForm($kurs);
        $this->checkChangeAllowance($kursForm);

        $post = Yii::$app->request->post();

        $tema = ArrayHelper::remove($post, 'tema');
        $chastTemy = ArrayHelper::remove($post, 'chast_temy');

        $zanyatie = $tema && $chastTemy
            ? $this->updateZanyatieTema($kursForm, $data, $nomer, $tema, $chastTemy)
            : $this->updateZanyatieAttributes($kursForm, $data, $nomer, $post);

        if (!$zanyatie)
            return false;

        return $zanyatie->getAttributes([
            'deduced_nazvanie',
            'tema_tip_raboty_nazvanie',
            'prepodavatel',
            'prepodavatel_peresechenie',
            'auditoriya_id',
            'auditoriya_nazvanie'
        ]);
    }

    /**
     * Delete action for AJAX
     *
     * @param integer $kurs
     * @param integer $data
     * @param integer $nomer
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @return boolean
     */
    public function actionDelete($kurs, $data, $nomer)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $this->checkChangeAllowance( $this->findKursForm($kurs) );

        $zanyatie = $this->findZanyatieByDataNomer($kurs, $data, $nomer);
        if (!$zanyatie)
            throw new NotFoundHttpException;

        if ($zanyatie && $this->deleteZanyatie($zanyatie)) //clean data-nomer or delete
            return true;

        return false;
    }

    public function actionPrepodavatelPeresechenie($kurs, $data, $nomer)
    {
        $zanyatie = Zanyatie::findOne(compact('kurs', 'data', 'nomer'));
        if (!$zanyatie)
            throw new NotFoundHttpException;

        $zanyatieSubQuery = \app\records\Zanyatie::find()
            ->select(['zanyatie_kurs' => 'kurs'])
            ->where([
                'and',
                ['<>', 'id', $zanyatie->id],
                [
                    'data' => $data,
                    'nomer' => $nomer,
                    'prepodavatel' => $zanyatie->prepodavatel
                ]
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => Kurs::find()->innerJoin(['z' => $zanyatieSubQuery], 'kurs.id = z.zanyatie_kurs'),
            'pagination' => false
        ]);

        return PrepodavatelPeresechenieContent::widget([
            'zanyatie' => $zanyatie,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @param integer $kurs
     * @return KursForm     
     * @throws NotFoundHttpException
     */
    private function findKursForm($kurs)
    {
        $kursForm = KursForm::findOne($kurs);
        if (!$kursForm)
            throw new NotFoundHttpException;

        return $kursForm;
    }

    /**
     * @param integer $kurs
     * @param integer $data
     * @param integer $nomer
     * @return Zanyatie|null
     */
    private function findZanyatieByDataNomer($kurs, $data, $nomer)
    {
        return Zanyatie::findByKurs($kurs)
            ->andWhere(['data' => $data, 'nomer' => $nomer])
            ->one();
    }

    /**
     * @param integer $tema
     * @param integer $chastTemy
     * @return Zanyatie|null
     */
    private function findZanyatieByChastTemy($tema, $chastTemy)
    {
        return Zanyatie::customFind()
            ->joinWith('zanyatiya_chastej_tem_rel', false)
            ->where([
                'tema' => $tema,
                'chast_temy' => $chastTemy
            ])
            ->one();
    }

    /**
     * @param KursForm $kurs
     * @throws BadRequestHttpException
     */
    private function checkChangeAllowance($kurs)
    {
        if (!$kurs->allowsZanyatiyaChange())
            throw new BadRequestHttpException;

        //todo
//        if (!Yii::$app->user->can('updateOwnRaspisanie', ['kurs' => $model]))
//            throw new ForbiddenHttpException;
    }

    /**
     * Delete zanyatie from schedule: clean data-nomer if potok, otherwise delete record
     *
     * @param Zanyatie $zanyatie
     * @return bool
     */
    private function deleteZanyatie($zanyatie)
    {
        if ($zanyatie->getIsPotok()) {
            $zanyatie->clearTime();

            return !$zanyatie->save();
        }

        $zct = $zanyatie->getZanyatiya_chastej_tem_rel()->one();

        return Yii::$app->db->transaction(function () use ($zanyatie, $zct) {
            if ($zct && !$zct->delete())
                return false;

            return $zanyatie->withDirectoriesDelete();
        });
    }

    /**
     * @param KursForm $kurs
     * @param string $data
     * @param integer $nomer
     * @param array $post
     * @return Zanyatie|null
     */
    private function updateZanyatieAttributes($kurs, $data, $nomer, $post)
    {
        $zanyatie = $this->findZanyatieByDataNomer($kurs->id, $data, $nomer);

        $result = $zanyatie !== null
            && $zanyatie->load($post, '')
            && $zanyatie->withDirectoriesSafeSave();

        return $result ? $zanyatie : null;
    }

    /**
     * @param KursForm $kurs
     * @param string $data
     * @param integer $nomer
     * @param integer $tema
     * @param integer $chastTemy
     * @return Zanyatie|null
     * @throws BadRequestHttpException
     * @throws NotSupportedException
     */
    private function updateZanyatieTema($kurs, $data, $nomer, $tema, $chastTemy)
    {
        $oldZanyatie = $this->findZanyatieByDataNomer($kurs->id, $data, $nomer);
        $newZanyatie = null;

        if (!$oldZanyatie) {
            $newZanyatie = $this->findZanyatieByChastTemy($tema, $chastTemy);
            $newZct = null;

            if ($newZanyatie) {
                $newZanyatie->data = $data;
                $newZanyatie->nomer = $nomer;
            } else { //create non-potok zanyatie
                list($newZanyatie, $newZct) = $this->createZanyatie($data, $nomer, $tema, $chastTemy, $kurs);
            }

            return Yii::$app->db->transaction(function () use ($newZanyatie, $newZct) {
                if (!$this->saveZanyatieWithZct($newZanyatie, $newZct)) // $newZct is not null if created non-potok
                    return null;

                return $newZanyatie;
            });
        }

        // has old

        $newZct = null;
        $oldNeedOp = null; //save or delete

        $newZanyatie = $this->findZanyatieByChastTemy($tema, $chastTemy);

        if ($newZanyatie) { // $this->zanyatieIsPotok($newZanyatie) is true for clean db
            if (!$newZanyatie->getIsPotok())
                throw new NotSupportedException('newZanyatie must be potok');

            if ($oldZanyatie->getIsPotok()) {
                $oldZanyatie->clearTime();
                $oldNeedOp = 'save';
            } else {
                $oldNeedOp = 'delete';
            }

            $newZanyatie->data = $data;
            $newZanyatie->nomer = $nomer;

        } else {
            if ($oldZanyatie->getIsPotok()) {
                $oldZanyatie->clearTime();
                $oldNeedOp = 'save';

                list($newZanyatie, $newZct) = $this->createZanyatie($data, $nomer, $tema, $chastTemy, $kurs);
            } else {
                $newZanyatie = $oldZanyatie;
                $temaRecord = Tema::findOne($tema);
                $newZanyatie->setDefaultsFromKurs($kurs, $temaRecord);

                $newZct = ArrayHelper::getValue($newZanyatie->zanyatiya_chastej_tem_rel, '0');
                if ($newZct === null)
                    throw new NotSupportedException('oldZanyatie must have Zct');

                $newZct->tema = $tema;
                $newZct->chast_temy = $chastTemy;
            }
        }

        return Yii::$app->db->transaction(function() use ($newZanyatie, $newZct, $oldZanyatie, $oldNeedOp) {
            if (!$this->saveZanyatieWithZct($newZanyatie, $newZct)) // $newZct is not null if non-potok created or existed
                return null;

            switch ($oldNeedOp) {
                case 'save': return $oldZanyatie->save() ? $newZanyatie : null;
                case 'delete': return $oldZanyatie->delete() ? $newZanyatie : null;
            }

            return null;
        });
    }

    /**
     * @param Zanyatie $zanyatie
     * @param ZanyatieChastiTemy|null $zct
     * @return bool
     * @throws BadRequestHttpException
     */
    private function saveZanyatieWithZct($zanyatie, $zct) {
        $zctsForCheck = $zct
            ? [$zct]
            : $zanyatie->getZanyatiya_chastej_tem_rel()->all();

        if ($zanyatie->getHasIntersectOthers($zctsForCheck))
            throw new BadRequestHttpException('Intersects with others');

        if (!$zanyatie->save())
            return false;

        if ($zct !== null) {
            $zct->zanyatie = $zanyatie->id;
            if (!$zct->save())
                return false;
        }

        return true;
    }

    /**
     * @param $data
     * @param $nomer
     * @param $tema
     * @param $chastTemy
     * @param KursForm $kurs
     * @return array
     */
    private function createZanyatie($data, $nomer, $tema, $chastTemy, $kurs) {
        $zanyatie = new Zanyatie();
        $zct = new ZanyatieChastiTemy();

        $zanyatie->data = $data;
        $zanyatie->nomer = $nomer;

        $zct->tema = $tema;
        $zct->chast_temy = $chastTemy;

        $temaRecord = Tema::findOne($tema);
        $zanyatie->setDefaultsFromKurs($kurs, $temaRecord);

        return [$zanyatie, $zct];
    }
}