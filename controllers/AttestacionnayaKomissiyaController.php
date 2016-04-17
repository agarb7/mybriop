<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 22.11.15
 * Time: 16:25
 */

namespace app\controllers;



use app\components\JsResponse;
use app\entities\AttestacionnayaKomissiya;
use app\entities\Dolzhnost;
use app\entities\DolzhnostAttestacionnojKomissii;
use app\entities\FizLico;
use app\entities\OtsenochnyjList;
use app\entities\Polzovatel;
use app\entities\RabotnikAttestacionnojKomissii;
use app\entities\RaspredelenieZayavlenijNaAttestaciyu;
use app\entities\StrukturaOtsenochnogoLista;
use app\entities\ZayavlenieNaAttestaciyu;
use app\enums\Rol;
use app\models\attestatsiya\Kurs;
use yii\base\Exception;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;

class AttestacionnayaKomissiyaController extends Controller
{
    public function actionIndex(){
        $komissii = AttestacionnayaKomissiya::find()->orderBy('nazvanie')->all();
        return $this->render('index',compact('komissii'));
    }

    public function actionGetKomissii(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $komissii = AttestacionnayaKomissiya::find()->orderBy('nazvanie')->all();
        return $komissii;
    }

    public function actionAddKomissiyu(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $nazvanie = $_REQUEST['nazvanie'];
        $newKomissiya = new AttestacionnayaKomissiya();
        $newKomissiya->nazvanie = $nazvanie;
        if ($newKomissiya->validate() && $newKomissiya->save()) return $newKomissiya;
        else Throw new Exception("Ошибка при сохранении данных");
        return false;
    }

    public function actionDeleteKomissiyu(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $id = $_REQUEST['id'];
        $deleteKomissiya =  AttestacionnayaKomissiya::findOne($id);
        if ($deleteKomissiya && $deleteKomissiya->delete()) return 1;
        else return 0;
    }

    public function actionCommitEditKomissii(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $id = $_REQUEST['id'];
        $new_nazvanie = $_REQUEST['new_nazvanie'];
        $result = [];
        if ($new_nazvanie  == ''){
            $result['type'] = 'error';
            $result['msg'] = 'Введите название';
        }
        else{
            $komissiya = AttestacionnayaKomissiya::findOne($id);
            $komissiya->nazvanie = $new_nazvanie;
            if ($komissiya->validate() && $komissiya->save()) {
                $result['type'] = 'success';
            }
            else{
                $result['type'] = 'error';
                $result['msg'] = 'Ошибка при сохранении данных, данные не прошли валидацию';
            }
        }
        return $result;
    }

    public function actionGetDolzhnosti(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $komissiya_id = $_REQUEST['komissiya'];
        $dolzhnosti = Dolzhnost::find()
            ->join('inner join','dolzhnost_attestacionnoj_komissii','dolzhnost.id=dolzhnost_attestacionnoj_komissii.dolzhnost')
            ->select('dolzhnost.*')
            ->where(['attestacionnaya_komissiya'=>$komissiya_id])
            ->orderBy('dolzhnost.nazvanie')
            ->all();
        return $dolzhnosti;
    }

    public function actionAddDolzhnostToKomissiya(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $existed_dolzhnost = DolzhnostAttestacionnojKomissii::find()
            ->where([
                'attestacionnaya_komissiya'=>$_REQUEST['komissiya_id'],
                'dolzhnost' => $_REQUEST['dolzhnost_id']
            ])->one();
        if (!$existed_dolzhnost) {
            $new_dolzhnost_v_komissii = new DolzhnostAttestacionnojKomissii();
            $new_dolzhnost_v_komissii->attestacionnaya_komissiya = $_REQUEST['komissiya_id'];
            $new_dolzhnost_v_komissii->dolzhnost = $_REQUEST['dolzhnost_id'];

            if ($new_dolzhnost_v_komissii->validate() && $new_dolzhnost_v_komissii->save()) {
                $dolzhnost = Dolzhnost::findOne($new_dolzhnost_v_komissii->dolzhnost);
                return [
                    'type' => 'success',
                    'data' => $dolzhnost
                ];
            } else {
                return [
                    'type' => 'error',
                    'msg' => 'Ошибка при сохранении данных'
                ];
            }
        }
        else{
            return[
                'type'=>'error',
                'msg'=>'Выбранная должность уже добавлена'
            ];
        }
    }

    public function actionDeleteDolzhnostFromKomissiya(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $dolzhnost_id = $_REQUEST['dolzhnost_id'];
        $komissiya_id = $_REQUEST['komissiya_id'];
        $dolzhnostAttestacionnoiKomissii = DolzhnostAttestacionnojKomissii::find()
            ->where([
                'attestacionnaya_komissiya'=>$komissiya_id,
                'dolzhnost' => $dolzhnost_id
            ])
            ->one();
        if ($dolzhnostAttestacionnoiKomissii && $dolzhnostAttestacionnoiKomissii->delete()) return 1;
        else return 0;
    }

    public function actionFizLicoList($q = null){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            $sql = 'select fiz_lico.id,
                           fiz_lico.familiya||\' \'||fiz_lico.imya||\' \'||fiz_lico.otchestvo||\', \'
                           ||organizaciya.nazvanie||\', \'||dolzhnost.nazvanie AS text
                    from fiz_lico
                    inner join rabota_fiz_lica on fiz_lico.id = rabota_fiz_lica.fiz_lico
                    inner join organizaciya on rabota_fiz_lica.organizaciya = organizaciya.id
                    inner join dolzhnost_fiz_lica_na_rabote on rabota_fiz_lica.id = dolzhnost_fiz_lica_na_rabote.rabota_fiz_lica
                    inner join dolzhnost on dolzhnost_fiz_lica_na_rabote.dolzhnost = dolzhnost.id
                    where lower(fiz_lico.familiya||\' \'||fiz_lico.imya||\' \'||fiz_lico.otchestvo) like :q';
            $q .= '%';
            $data = \Yii::$app->db->createCommand($sql)->bindValue(':q',mb_strtolower($q))->queryAll();
            $out['results'] = array_values($data);
        }
        return $out;
    }

    public function actionGetRabotnikovKomissii(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $komissiya_id = $_REQUEST['komissiya_id'];
        $list = [];
        if ($komissiya_id) {
            $list = RabotnikAttestacionnojKomissii::find()
                ->joinWith('fizLicoRel')
                ->where(['rabotnik_attestacionnoj_komissii.attestacionnaya_komissiya' => $komissiya_id])
                ->asArray()->all();
        }
        return $list;
    }

    public function actionAddRabotnikaKomissii()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['data'=>[],'msg'=>'','type'=>'success'];
        $komissiya_id = $_REQUEST['komissiya_id'];
        $rabotnik_id = $_REQUEST['rabotnik_id'];
        $existed_rabotnik_komissii = RabotnikAttestacionnojKomissii::find()->where([
            'attestacionnaya_komissiya' => $komissiya_id,
            'fiz_lico' => $rabotnik_id
        ])->one();
        if (!$existed_rabotnik_komissii) {
            $new_rabotnik_komissii = new RabotnikAttestacionnojKomissii();
            $new_rabotnik_komissii->attestacionnaya_komissiya = $komissiya_id;
            $new_rabotnik_komissii->fiz_lico = $rabotnik_id;
            $new_rabotnik_komissii->predsedatel = false;
            $polzovatel = Polzovatel::find()->where(['fiz_lico'=>$rabotnik_id])->one();
            if (!$polzovatel->isThereRol(Rol::SOTRUDNIK_ATTESTACIONNOJ_KOMISSII))
                $polzovatel->addRol(Rol::SOTRUDNIK_ATTESTACIONNOJ_KOMISSII);
                //$polzovatel->roliAsArray = array_merge($polzovatel->roliAsArray,[Rol::SOTRUDNIK_ATTESTACIONNOJ_KOMISSII]);
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                $new_rabotnik_komissii->save();
                $polzovatel->save();
                $transaction->commit();
                $rabotnik = RabotnikAttestacionnojKomissii::find()
                    ->joinWith('fizLicoRel')
                    ->where(['fiz_lico.id' => $rabotnik_id])
                    ->asArray()->one();
                $result['rabotnik'] = $rabotnik;
            }
            catch (Exception $e){
                $transaction->rollBack();
                $result['type'] = 'error';
                $result['msg'] = 'Ошибка при сохранени данных';
            }
//            if ($new_rabotnik_komissii->validate() && $new_rabotnik_komissii->save()) {
//
//            } else {
//
//            }
        }
        else{
            $result['type'] = 'warning';
            $result['msg'] = 'Данный работник уже добавлен в состав выбранной комиссии';
        }
        return $result;
    }

    public function actionDeleteRabotnikaKomissii(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $id = $_REQUEST['id'];
        $deleting_rabotnik = RabotnikAttestacionnojKomissii::findOne($id);
        $polzovatel = Polzovatel::find()->where(['fiz_lico' => $deleting_rabotnik->fiz_lico])->one();
        $countOthers = RabotnikAttestacionnojKomissii::find()
            ->where(['fiz_lico' => $deleting_rabotnik->fiz_lico])
            ->andWhere(['!=','id',$deleting_rabotnik->id])
            ->count();
        if ($countOthers == 0)
            $polzovatel->deleteRol(Rol::SOTRUDNIK_ATTESTACIONNOJ_KOMISSII);
        $countOthersPredsedatel = RabotnikAttestacionnojKomissii::find()
            ->where(['fiz_lico' => $deleting_rabotnik->fiz_lico])
            ->andWhere(['!=','id',$deleting_rabotnik->id])
            ->andWhere(['predsedatel' => true])
            ->count();
        if ($countOthersPredsedatel == 0)
            $polzovatel->deleteRol(Rol::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII);
        $raspredelenieZayavlenij = RaspredelenieZayavlenijNaAttestaciyu::find()
            ->where(['rabotnik_attestacionnoj_komissii' => $deleting_rabotnik->id])
            ->one();
        $result = new JsResponse();
        if ($raspredelenieZayavlenij){
            $result->type = JsResponse::ERROR;
            $result->msg = 'На данного работника задано распределение заявлений';
        }
        else {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if ($deleting_rabotnik)
                    $deleting_rabotnik->delete();
                $polzovatel->save();
                $transaction->commit();
                $result->msg = JsResponse::MSG_OPERATION_SUCCESS;
            } catch (Exception $e) {
                $transaction->rollBack();
                $result->type = JsResponse::ERROR;
                $result->msg = $e->getMessage();// JsResponse::MSG_OPERATION_ERROR.' Запись не удалена.';
            }
        }
        return $result;
    }

    public function actionChangePredsedatelKomissii(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $id = $_REQUEST['id'];
        $rabotnik = RabotnikAttestacionnojKomissii::findOne($id);
        $result = new JsResponse();
        $rabotnik->predsedatel = !$rabotnik->predsedatel;
        $polzovatel = Polzovatel::find()->where(['fiz_lico'=>$rabotnik->fiz_lico])->one();
        if ($rabotnik->predsedatel){
            if (!$polzovatel->isThereRol(Rol::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII))
                $polzovatel->addRol(Rol::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII);
                //$polzovatel->roliAsArray = array_merge($polzovatel->roliAsArray,[Rol::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII]);
            $current_predsedatel = RabotnikAttestacionnojKomissii::find()->where([
                'predsedatel'=>true,
                'attestacionnaya_komissiya'=>$rabotnik->attestacionnaya_komissiya
            ])->one();
            $current_predsedatel_polzavatel = false;
            if ($current_predsedatel) {
                $current_predsedatel->predsedatel = false;
                $current_predsedatel_polzavatel = Polzovatel::find()->where(['fiz_lico' => $current_predsedatel->fiz_lico])->one();
                $countOthersPredsedatel = RabotnikAttestacionnojKomissii::find()
                    ->where(['fiz_lico' => $current_predsedatel->fiz_lico])
                    ->andWhere(['!=','id',$current_predsedatel->id])
                    ->andWhere(['predsedatel' => true])
                    ->count();
                if ($countOthersPredsedatel == 0)
                    $current_predsedatel_polzavatel->deleteRol(Rol::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII);
            }
        }
        else{
            $countOthersPredsedatel = RabotnikAttestacionnojKomissii::find()
                ->where(['fiz_lico' => $rabotnik->fiz_lico])
                ->andWhere(['!=','id',$rabotnik->id])
                ->andWhere(['predsedatel' => true])
                ->count();
            if ($countOthersPredsedatel == 0)
                $polzovatel->deleteRol(Rol::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII);
        }
        $is_error = false;
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $rabotnik->save(false);
            $polzovatel->save(false);
            if ($rabotnik->predsedatel && $current_predsedatel){
                $current_predsedatel->save(false);
            }
            if ($rabotnik->predsedatel && $current_predsedatel_polzavatel){
                $current_predsedatel_polzavatel->save(false);
            }
            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            $is_error = true;
            throw $e;
        }
        if ($is_error){
            $result->type = JsResponse::ERROR;
            $result->msg = JsResponse::MSG_OPERATION_ERROR;
        }
        else{
            $result->msg = JsResponse::MSG_OPERATION_SUCCESS;
            $result->data = $rabotnik;
        }
        return $result;
    }
}