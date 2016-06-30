<?php
namespace app\upravlenie_kursami\raspisanie\controllers;

use app\modules\upravlenie_kursami\modules\raspisanie\widgets\PrepodavatelPeresechenieContent;
use app\records\Auditoriya;
use app\upravlenie_kursami\models\FizLico;
use app\upravlenie_kursami\raspisanie\models\Kurs;
use Yii;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
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

        return $this->render('index', [
            'gridData' => $gridData,
            'kursForm' => $kursForm,
            'kursRecord' => $kursRecord,
            'auditorii' => $auditorii,
            'prepodavateli' => $prepodavateli
        ]);
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
        
        $zanyatie = $this->getZanyatie($kurs, $data, $nomer);

        if (!$zanyatie->load(Yii::$app->request->post(), ''))
            return false;

        if ($zanyatie->getIsNewRecord())
            $zanyatie->setDefaultsFromKurs($kursForm);

        if (!$zanyatie->withDirectoriesSafeSave())
            return false;

        return $zanyatie->getAttributes([
            'tema_nazvanie_chast',
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
     * @throws \Exception
     * @return array
     */
    public function actionDelete($kurs, $data, $nomer)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $this->checkChangeAllowance( $this->findKursForm($kurs) );

        if (!$this->findZanyatie($kurs, $data, $nomer)->withDirectoriesSafeDelete())
            return false;

        return true;
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
     * @return Zanyatie
     */
    private function getZanyatie($kurs, $data, $nomer)
    {         
        return Zanyatie::findOne(compact('kurs', 'data', 'nomer'))
            ?: new Zanyatie(compact('kurs', 'data', 'nomer'));
    }

    /**
     * @param integer $kurs
     * @param integer $data
     * @param integer $nomer
     * @return Zanyatie
     * @throws NotFoundHttpException
     */
    private function findZanyatie($kurs, $data, $nomer)
    {
        $zanyatie = Zanyatie::findOne(compact('kurs', 'data', 'nomer'));
        if (!$zanyatie)
            throw new NotFoundHttpException;
        
        return $zanyatie;
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
}