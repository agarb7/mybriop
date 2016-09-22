<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 30.07.15
 * Time: 15:06
 */

namespace app\controllers;


use app\components\JsResponse;
use app\entities\Dolzhnost;
use app\entities\EntityQuery;
use app\entities\Fajl;
use app\entities\FizLico;
use app\entities\Organizaciya;
use app\entities\OtklonenieZayavleniyaNaAttestaciyu;
use app\entities\OtsenochnyjListZayavleniya;
use app\entities\Polzovatel;
use app\entities\RabotaFizLica;
use app\entities\Vedomstvo;
use app\entities\VremyaProvedeniyaAttestacii;
use app\entities\ZayavlenieNaAttestaciyu;
use app\enums\Rol;
use app\enums\StatusZayavleniyaNaAttestaciyu;
use app\enums\TipOtraslevogoSoglashenijya;
use app\globals\ApiGlobals;
use app\helpers\ArrayHelper;
use app\models\attestatsiya\AttestaciyaSpisokFilter;
use app\models\attestatsiya\AttestatciyaList;
use app\models\attestatsiya\DolzhnostFizLica;
use app\models\attestatsiya\Kurs;
use app\models\attestatsiya\OtraslevoeSoglashenie;
use app\models\attestatsiya\Registraciya;
use app\models\attestatsiya\VissheeObrazovanie;
use kartik\mpdf\Pdf;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;
use \Yii;

class AttestaciyaController extends Controller
{
    public function actionIndex(){
        $fizLico = ApiGlobals::getFizLicoPolzovatelyaId();
        $list = ZayavlenieNaAttestaciyu::find()
            ->joinWith('vremyaProvedeniyaAttestaciiRel')
            ->joinWith('attestacionnoeVariativnoeIspytanie2Rel')
            ->joinWith('attestacionnoeVariativnoeIspytanie3Rel')
            ->joinWith('portfolioFajlRel')
            ->joinWith('varIspytanie2FajlRel')
            ->joinWith('varIspytanie3FajlRel')
            ->joinWith('prezentatsiyaFajlRel')
            ->joinWith('otraslevoeSoglashenieZayavleniyaRel')
            ->where(['fiz_lico'=>$fizLico])->all();
        $otsenki = [];
        $sql = 'select z.id as zayavlenie_na_attestaciyu,
                      count(r.id) as rabotnik_count,
                      COALESCE (SUM(case when r.status = \'podpisano\' then 1 else 0 end),0) as podpisannie_otsenki_count,
                      COALESCE (avg_ball.ball,0) as avg_ball
                from zayavlenie_na_attestaciyu as z
                      LEFT JOIN raspredelenie_zayavlenij_na_attestaciyu as r on z.id = r.zayavlenie_na_attestaciyu
                      LEFT JOIN rabotnik_attestacionnoj_komissii as ra on r.rabotnik_attestacionnoj_komissii = ra.id
                      LEFT JOIN (
                            select zayavlenie_na_attestaciyu, cast(avg(t.bally) as float) / count(rabotnik_komissii) as ball
                            from (
                                SELECT
                                      alz.id,
                                      alz.rabotnik_komissii,
                                      alz.zayavlenie_na_attestaciyu,
                                      sum(solz.bally) AS bally
                                FROM otsenochnyj_list_zayavleniya AS alz
                                      INNER JOIN struktura_otsenochnogo_lista_zayvaleniya AS solz ON alz.id = solz.otsenochnyj_list_zayavleniya
                                      INNER JOIN zayavlenie_na_attestaciyu AS z ON alz.zayavlenie_na_attestaciyu = z.id
                                WHERE solz.uroven = 1
                                GROUP BY alz.id, alz.zayavlenie_na_attestaciyu, alz.rabotnik_komissii
                            ) as t
                            group by zayavlenie_na_attestaciyu
                      ) as avg_ball on z.id = avg_ball.zayavlenie_na_attestaciyu
                where z.fiz_lico = :fiz_lico
                group by z.id, avg_ball.ball';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':fiz_lico', $fizLico)->queryAll();
        foreach ($res as $item) {
            $otsenki[$item['zayavlenie_na_attestaciyu']] = $item;
        }
        return $this->render('index',compact('list','otsenki'));
    }

    public function actionRegistraciya(){
        $post = \Yii::$app->request->post();
        $messages = [];
        if ($post) {
            $registraciya = new Registraciya();
            $visshieObrazovaniya = [];
            if (isset($post['VissheeObrazovanie']))
                foreach ($post['VissheeObrazovanie'] as $k=>$v){
                    $visshieObrazovaniya[$k] = new VissheeObrazovanie();
                };
            $kursy = [];
            if (isset($post['Kurs']))
                foreach ($post['Kurs'] as $k=>$v){
                    $kursy[$k] = new Kurs();
                };
            $otraslevoeSoglashenie = [];
            if (isset($post['OtraslevoeSoglashenie']))
                foreach ($post['OtraslevoeSoglashenie'] as $k=>$v){
                    $otraslevoeSoglashenie[$k] = new OtraslevoeSoglashenie();
                };
            $registraciya->visshieObrazovaniya = $visshieObrazovaniya;
            $registraciya->kursy = $kursy;
            $registraciya->otraslevoeSoglashenie = $otraslevoeSoglashenie;
            $is_error = false;
            if (!($registraciya->load($post) && $registraciya->validate())) {
                $is_error = true;
            }
            if ($visshieObrazovaniya && !(
                    VissheeObrazovanie::loadMultiple($registraciya->visshieObrazovaniya, $post) &&
                    VissheeObrazovanie::validateMultiple($registraciya->visshieObrazovaniya)
                 )) {
                $is_error = true;
                //var_dump('vo');
            }
            if ($kursy && !(
                    Kurs::loadMultiple($registraciya->kursy, $post) &&
                    Kurs::validateMultiple($registraciya->kursy)
                )) {
                $is_error = true;
                //var_dump('kursy');
            }
            if ($otraslevoeSoglashenie && !(
                    OtraslevoeSoglashenie::loadMultiple($registraciya->otraslevoeSoglashenie, $post) &&
                    OtraslevoeSoglashenie::validateMultiple($registraciya->otraslevoeSoglashenie)
                )) {
                $is_error = true;
                //var_dump('so');
            }
            //var_dump($visshieObrazovaniya);die();
            if (!$is_error) {
               if (!$registraciya->save()) {
                     //\Yii::$app->session->setFlash('danger','Данные нее сохранены! Ошибка выполнения запроса к базе данных!');
                     $messages[] = ['type'=>'danger','msg'=>'Данные нее сохранены! Ошибка выполнения запроса к базе данных!'];
               }
               else{
                   \Yii::$app->session->setFlash('success','Данные  успешно сохранены!');
                   $this->redirect('/attestaciya/registraciya?zid='.$registraciya->id);
                   //$messages[] = ['type'=>'success','msg'=>'Данные  успешно сохранены!'];
               }
            }
            else{
                $messages[] = ['type'=>'danger','msg'=>'Ошибка валидации данных'];
                //\Yii::$app->session->setFlash('danger','Ошибка валидации данных');
                //var_dump('loading error');
            }
        }
        else{
            $zayvlenieId = null;
            if (isset($_GET['zid'])) $zayvlenieId = $_GET['zid'];
            $registraciya = new Registraciya($zayvlenieId);
            $registraciya->fizLicoId  = ApiGlobals::getFizLicoPolzovatelyaId();
            $registraciya->visshieObrazovaniya = VissheeObrazovanie::getObrazovaniya($registraciya->fizLicoId,$zayvlenieId);
            $registraciya->kursy = Kurs::getObrazovaniya($registraciya->fizLicoId,$zayvlenieId);
            $registraciya->otraslevoeSoglashenie = OtraslevoeSoglashenie::getByZayvlenie($zayvlenieId);
        }
        return $this->render('registraciya',compact('registraciya','messages'));
    }

    public function actionAddDolzhnost(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $fizLicoId = isset($_POST['fizLicoId']) ? $_POST['fizLicoId'] : 1;
        $model =  new DolzhnostFizLica();
        $model->fizLicoId = $fizLicoId;
        $model->organizaciyaAdress = 421574;
        $model->organizaciyaVedomstvo = 18;
        return $this->renderAjax('dolzhnost',compact('model'));
        //return json_encode($this->renderPartial('dolzhnost',compact('model')));
    }

    public function actionSubmitAddDolzhnost(){
        $model = new DolzhnostFizLica();
        $post = \Yii::$app->request->post();
        if ($model->load($post) && $model->validate() && $newDolzhnost = $model->addDolzhnost()){
            $answer['result'] = true;
            $answer['data'] = $newDolzhnost;
            return json_encode($answer);
        }
        $answer['result'] = false;
        return json_encode($this->renderAjax('dolzhnost',compact('model')));
    }

    public function actionAddVisheeObrazovanie(){
        $num = isset($_POST['num']) ? $_POST['num'] : '0';
        $model = new VissheeObrazovanie();
        return json_encode($this->renderAjax('vissheeObrazovanie',compact('model','num')));
    }

    public function actionAddKurs(){
        $num = isset($_POST['num']) ? $_POST['num'] : '0';
        $model = new Kurs();
        return json_encode($this->renderAjax('kurs',compact('model','num')));
    }

    public function actionAddOtraslevoeSoglashenie(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $num = isset($_POST['num']) ? $_POST['num'] : '0';
        $model = new OtraslevoeSoglashenie();
        return $this->renderAjax('otraslevoeSoglashenie',compact('num','model'));
    }

    public function actionList(){
        $filterModel = new AttestaciyaSpisokFilter();
        $dataProvider = $filterModel->search(\Yii::$app->request->get());
        return $this->render('list',compact('filterModel','dataProvider'));
    }

    public function actionZayavlenie()
    {
        $id = $_REQUEST['id'];
        if (isset($_REQUEST['isAjax'])) $isAjax = $_REQUEST['isAjax'];
        else $isAjax = false;

        $zayavlenie = ZayavlenieNaAttestaciyu::find()
            ->joinWith('dolzhnostRel')
            ->joinWith('attestacionnoeVariativnoeIspytanie2Rel')
            ->joinWith('attestacionnoeVariativnoeIspytanie3Rel')
            ->joinWith('vremyaProvedeniyaAttestaciiRel')
            ->joinWith('organizaciyaRel')
            ->joinWith('attestaciyaFajlRel')
            ->joinWith('kopiyaTruidovoiajlRel')
            ->joinWith('obrazovaniyaRel.obrazovanieOrganizaciyaRel')
            ->joinWith('obrazovaniyaRel.obrazovanieFajlRel')
            ->joinWith('obrazovaniyaRel.obrazovanieKvalifikaciyaRel')
            ->joinWith('kursyRel.kursOrganizaciyaRel')
            ->joinWith('kursyRel.kursFajlRel')
            ->joinWith('kursyRel.kursKvalifikaciyaRel')
            ->joinWith('attestaciyaFajlRel')
            ->joinWith('svedeniyaOSebeFajlRel')
            ->joinWith('kopiyaTruidovoiajlRel')
            ->joinWith('otraslevoeSoglashenieZayavleniyaRel.otraslevoeSoglashenieRel')
            ->joinWith('otraslevoeSoglashenieZayavleniyaRel.fajlRel')
            ->joinWith('fizLicoRel')
            ->where(['zayavlenie_na_attestaciyu.id' => $id])
            ->one();

        if ($isAjax) {
            \Yii::$app->response->format = 'json';
            return $this->renderAjax('zayavlenie', compact('zayavlenie'));
        }
        else
            return $this->render('zayavlenie',compact('zayavlenie'));
    }

    public function actionAcceptZayavlenie(){
        /**
         * @var ZayavlenieNaAttestaciyu $zayavlenie
         */
        $id = $_REQUEST['q'];
        //$date_s = $_REQUEST['date_s'];
        //$date_po = $_REQUEST['date_po'];
        $zayavlenie = ZayavlenieNaAttestaciyu::find()
            ->where(['zayavlenie_na_attestaciyu.id'=>$id])
            ->one();
        $answer = [];
        if ($zayavlenie){
            $zayavlenie->status = StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII;
            $zayavlenie->vremya_smeny_statusa = date('Y-m-d H:i:s');
            if ($zayavlenie->save()) {
                $answer['result'] = 'success';
                $model = ZayavlenieNaAttestaciyu::find()
                    ->joinWith('dolzhnostRel')
                    ->joinWith('organizaciyaRel')
                    //->joinWith('vremyaProvedeniyaAttestaciiRel')
                    ->where(['zayavlenie_na_attestaciyu.id'=>$id])
                    ->one();
                $email = FizLico::getEmailById($zayavlenie->fiz_lico);
                \Yii::$app->mailer->compose('/attestaciya/podtverzhdeno.php',compact('model','date_s','date_po'))
                    ->setTo($email)
                    ->send();
            }
            else $answer['result'] = 'error';
        }
        else $answer['result'] = 'error';
        \Yii::$app->response->format = 'json';
        return $answer;
    }

    public function actionCancelAcceptanceZayavelnie(){
        $id = $_REQUEST['q'];
        $zayavlenie = ZayavlenieNaAttestaciyu::findOne($id);
        $answer = [];
        if ($zayavlenie){
            $zayavlenie->status = StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII;
            if ($zayavlenie->save()) $answer['result'] = 'success';
            else $answer['result'] = 'error';
        }
        else $answer['result'] = 'error';
        \Yii::$app->response->format = 'json';
        return $answer;
    }

    public function actionOtklonitZayavlenie(){
        $id = $_REQUEST['q'];
        $comment = $_REQUEST['comment'];
        $zayavlenie = ZayavlenieNaAttestaciyu::findOne($id);
        $answer = [];
        if ($zayavlenie){
            $zayavlenie->status = StatusZayavleniyaNaAttestaciyu::OTKLONENO;
            if ($zayavlenie->save()) {
                $answer['result'] = 'success';
                $model = ZayavlenieNaAttestaciyu::find()
                    ->joinWith('dolzhnostRel')
                    ->joinWith('organizaciyaRel')
                    ->where(['zayavlenie_na_attestaciyu.id'=>$id])
                    ->one();
                $email = FizLico::getEmailById($zayavlenie->fiz_lico);
                \Yii::$app->mailer->compose('/attestaciya/otkloneno.php',compact('model','comment'))
                    ->setTo($email)
                    ->send();
            }
            else $answer['result'] = 'error';
        }
        else $answer['result'] = 'error';
        \Yii::$app->response->format = 'json';
        return $answer;
    }

    public function actionDeleteZayavelnie(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $id = $_REQUEST['id'];
        if ($id){
            $zayavlenie = ZayavlenieNaAttestaciyu::findOne($id);    
            if ($zayavlenie){
                $zayavlenie->delete();
            }
            else{
                $response->type = JsResponse::ERROR;
                $response->msg = 'Заявление с данным номер не найдено';    
            }
        }
        else{
            $response->type = JsResponse::ERROR;
            $response->msg = 'Укажите номер заявления';
        }
        return $response;
    }

    public function actionRabotaOrg()
    {
        //Yii::$app->response->format = Response::FORMAT_JSON;

        $parents = Yii::$app->request->post('depdrop_parents');

        $vedomstvo_id = $parents[0];
        $ao_id = $parents[1];
        $oid = Yii::$app->request->get('oid');
        $params['valueColumn'] = 'nazvanie';
        $params['selected'] = $oid;

        echo json_encode(Organizaciya::findByVedomstvoAndAdres($vedomstvo_id, $ao_id)
            ->commonOnly()
            ->formattedAll(EntityQuery::DEP_DROP_AJAX, $params));
        die();
    }

    public function actionSaveIspytanie()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['result'=>'error'];
        $zayvlenie_id = isset($_REQUEST['zayavlenie_id']) ? $_REQUEST['zayavlenie_id'] : false;
        $file_id = isset($_REQUEST['file_id']) ? $_REQUEST['file_id'] : false;
        $tip = isset($_REQUEST['tip']) ? $_REQUEST['tip'] : false;
        if ($zayvlenie_id){
            $zayvlenie = ZayavlenieNaAttestaciyu::findOne($zayvlenie_id);
            if ($tip == 'portfolio') $zayvlenie->portfolio = $file_id;
            if ($tip == 'var_isp2') $zayvlenie->var_ispytanie_2_fajl = $file_id;
            if ($tip == 'var_isp3') $zayvlenie->var_ispytanie_3_fajl = $file_id;
            if ($tip == 'prezentatsiya') $zayvlenie->prezentatsiya = $file_id;
            if ($zayvlenie->save()){
                $result['result'] = 'success';
                $file = Fajl::findOne($file_id);
                $result['html'] = $file->getFileLink();
            }
        }
        return $result;
    }

    public function actionPrintZayavlenie($id = false){
        if (!$id) throw new Exception('id parameter is required');
        $zayavlenie = ZayavlenieNaAttestaciyu::find()->joinWith('organizaciyaRel')
            ->joinWith('dolzhnostRel')
            ->joinWith('vremyaProvedeniyaAttestaciiRel')
            ->joinWith('attestacionnoeVariativnoeIspytanie3Rel')
            ->joinWith('fizLicoRel')
            ->joinWith('obrazovaniyaRel.obrazovanieOrganizaciyaRel')
            ->joinWith('obrazovaniyaRel.obrazovanieKvalifikaciyaRel')
            ->joinWith('kursyRel.kursOrganizaciyaRel')
            ->joinWith('otraslevoeSoglashenieZayavleniyaRel.otraslevoeSoglashenieRel')
            ->where(['zayavlenie_na_attestaciyu.id'=>$id])
            ->one();
        $content = $this->renderPartial('_printZayavlenie',compact('zayavlenie'));
        $indent = 3;
        $css = '
                body{
                   font-family:"Times New Roman","serif";
                }
                .paragraph{
                    text-indent: '.$indent.'em;
                    text-align:justify;
                }
                .center{
                 text-align:center;
                }
                .tb {border-collapse: collapse}
                .tb td {padding: 5px;border: 1px solid #000}
                .inline-block{
                    display: inline-block;
                }
                .indent{padding-left: '.$indent.'em}

                .double-indent{padding-left: '.(2*$indent).'em}

                .indent-block{
                    margin-left: '.$indent.'em;
                }
                ';

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
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
            'options' => ['title' => 'Заявление в аттестационную комиссию'],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetFooter'=>[''],
            ]
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionGetOtkloneniyaAttestacii(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $list = OtklonenieZayavleniyaNaAttestaciyu::find()->asArray()->all();
        foreach ($list as $item) {
            $result[$item['id']] = $item['text'];
        }
        return $result;
    }

    public function actionMoveToOa(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $id = Yii::$app->request->post('id');
        if (!$id) {
            $response->type = JsResponse::ERROR;
        }
        if ($response->type != JsResponse::ERROR){
            /**
             * @var ZayavlenieNaAttestaciyu $zayavlenie
             */
            $zayavlenie = ZayavlenieNaAttestaciyu::findOne(['id'=>$id]);
            $zayavlenie->status = StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII;
            $zayavlenie->vremya_smeny_statusa = date('Y-m-d H:i:s');
            if (!$zayavlenie->save()){
                $response->type = JsResponse::ERROR;
            }
        }
        if ($response->type == JsResponse::ERROR){
            $response->msg = JsResponse::MSG_OPERATION_ERROR;
        }
        return $response;
    }

    public function actionChangeVremyaProvedeniya(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $id = Yii::$app->request->post('id');
        $vremyaId = Yii::$app->request->post('vremya_id');
        /**
         * @var ZayavlenieNaAttestaciyu $zayavlenie
         */
        $zayavlenie = ZayavlenieNaAttestaciyu::findOne(['id'=>$id]);
        $zayavlenie->vremya_provedeniya = $vremyaId;
        $zayavlenie->status = StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII;
        $period = VremyaProvedeniyaAttestacii::findOne($vremyaId);
        if (!$zayavlenie->save()){
            $response->type = JsResponse::ERROR;
            $response->msg = JsResponse::MSG_OPERATION_ERROR;
        }
        else{
            $email = FizLico::getEmailById($zayavlenie->fiz_lico);
            \Yii::$app->mailer->compose('/attestaciya/vremya-izmeneno.php',compact('period'))
                ->setTo($email)
                ->send();
        }
        return $response;
    }

    public function actionPrintDostizheniya($id = false){
        if (!$id) throw new Exception('id parameter is required');
        $zayavlenie = ZayavlenieNaAttestaciyu::find()
            ->joinWith('fizLicoRel')
            ->where(['zayavlenie_na_attestaciyu.id'=>$id])
            ->one();
        $content = $this->renderPartial('_printDostizheniya',compact('zayavlenie'));
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
                .indent{padding-left: '.$indent.'em}

                .double-indent{padding-left: '.(2*$indent).'em}

                .indent-block{
                    margin-left: '.$indent.'em;
                }
                .bold{
                    font-weight: bold;
                }
                ';

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
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
            'options' => ['title' => 'Личные достижения'],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetFooter'=>[''],
            ]
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
    }
}