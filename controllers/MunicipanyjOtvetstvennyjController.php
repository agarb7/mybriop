<?php

namespace app\controllers;

use app\components\Controller;
use app\components\JsResponse;
use app\entities\AdresnyjObjekt;
use app\entities\MunicipalnyjOtvestvennyj;
use app\entities\Polzovatel;
use app\entities\VremyaProvedeniyaAttestacii;
use app\entities\ZayavlenieNaAttestaciyu;
use app\enums2\Rol;
use app\globals\ApiGlobals;
use yii\web\Response;

class MunicipanyjOtvetstvennyjController extends Controller
{
    public function actionSostav()
    {
        return $this->render('sostav.php');
    }

    public function actionGetDistricts()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $result = new JsResponse();
        $data =  AdresnyjObjekt::find()
                    ->joinWith('municipalnyeOtvestvennyeRel.fizLicoRel')
                    ->where(['adresnyj_objekt.roditel' => 1205706, 'adresnyj_objekt.uroven' => 'rajon'])
                    ->orWhere(['adresnyj_objekt.id' => 421574])
                    ->orderBy('adresnyj_objekt.oficialnoe_nazvanie')
                    ->asArray()
                    ->all();
        $result->data = $data;
        return $result;
    }

    public function actionSetMunicipalnogoOtvestvennogo(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $result = new JsResponse();
        $district_id = $_REQUEST['district_id'];
        $fiz_lico = $_REQUEST['fiz_lico'];
        $existed_mo = MunicipalnyjOtvestvennyj::find()->where([
            'district_id' => $district_id,
            'fiz_lico' => $fiz_lico
        ])->one();
        if (!$existed_mo) {
            $mo = new MunicipalnyjOtvestvennyj();
            $mo->district_id = $district_id;
            $mo->fiz_lico = $fiz_lico;
            $polzovatel = Polzovatel::find()->where(['fiz_lico'=>$fiz_lico])->one();
            $oldMo = MunicipalnyjOtvestvennyj::find()->where(['district_id' => $district_id])->one();
            $oldPolzovatel = false;
            if ($oldMo){
                $oldPolzovatel = Polzovatel::find()->where([ 'fiz_lico' => $oldMo->fiz_lico])->one();
                $count = MunicipalnyjOtvestvennyj::find()->where(['fiz_lico' => $oldMo->fiz_lico])->count();
                if ($count == 1) {
                    $oldPolzovatel->deleteRol(Rol::MUNICIPALNYJ_OTVESTVENNYJ);
                }
            }
            if (!$polzovatel->isThereRol(Rol::MUNICIPALNYJ_OTVESTVENNYJ))
                $polzovatel->addRol(Rol::MUNICIPALNYJ_OTVESTVENNYJ);
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                if ($oldPolzovatel){
                    $oldPolzovatel->save();
                }
                if ($oldMo){
                    $oldMo->delete();
                }
                $mo->save();
                $polzovatel->save();
                $transaction->commit();
                $savedMO = MunicipalnyjOtvestvennyj::find()
                    ->joinWith('fizLicoRel')
                    ->where(['fiz_lico' => $fiz_lico, 'district_id' => $district_id])
                    ->asArray()
                    ->one();
                $result->data = $savedMO;
            }
            catch (Exception $e){
                $transaction->rollBack();
                $result->type = JsResponse::ERROR;
                $result->msg = JsResponse::MSG_OPERATION_ERROR;
                //$result['help'] = $e->getMessage();
            }
        }
        else{
            $result->type = JsResponse::ERROR;
            $result->msg = 'Данный работник уже является муниципальным отвественным в этом районе';
        }
        return $result;
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

    public function actionList()
    {
        $periods = VremyaProvedeniyaAttestacii::find()->all();
        return $this->render('list.php', compact('periods'));
    }

    public function actionGetZayavleniya()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();

        $fiz_lico = ApiGlobals::getFizLicoPolzovatelyaId();
        $districts = MunicipalnyjOtvestvennyj::find()
            ->where(['fiz_lico' => $fiz_lico])
            ->select('district_id')
            ->column();
//var_dump($districts);die();
        $periodId = $_REQUEST['period'];//\Yii::$app->request->post('period_id');
        $data = ZayavlenieNaAttestaciyu::find()
            ->joinWith('organizaciyaRel.adresAdresnyjObjektRel')
            ->where(['adresnyj_objekt.id' => $districts])
            ->orWhere(['adresnyj_objekt.roditel' => $districts])
            ->andWhere(['zayavlenie_na_attestaciyu.vremya_provedeniya' => $periodId])
            ->asArray()
        ->all();
        $response->data = $data;
        return $response;
    }

    public function accessRules()
    {
        return [
            '*' => '*'
        ];
    }
}