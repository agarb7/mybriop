<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 06.01.16
 * Time: 12:25
 */

namespace app\controllers;


use app\components\Controller;
use app\components\JsResponse;
use app\entities\DolzhnostAttestacionnojKomissii;
use app\entities\OtsenochnyjListZayavleniya;
use app\entities\RabotnikAttestacionnojKomissii;
use app\entities\StrukturaOtsenochnogoListaZayvaleniya;
use app\entities\VremyaProvedeniyaAttestacii;
use app\entities\ZayavlenieNaAttestaciyu;
use app\enums\Rol;
use app\enums2\StatusOtsenochnogoLista;
use app\globals\ApiGlobals;
use app\models\rukovoditel_attestacionnoj_komissii\RabotnikKomissii;
use app\models\rukovoditel_attestacionnoj_komissii\Zayavlenie;
use yii\db\Query;
use \yii\web\Response;

class RukovoditelKomissiiController extends Controller
{
    public function actionIndex()
    {

        $periods = VremyaProvedeniyaAttestacii::find()->all();
        return $this->render('index.php',compact('periods'));
    }

    public function actionGetZayavleniya($period)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $sql = 'SELECT zna.*,
                       string_agg(rzna.rabotnik_attestacionnoj_komissii::character varying,\',\') as raspredelenie
                FROM zayavlenie_na_attestaciyu as zna
                LEFT JOIN raspredelenie_zayavlenij_na_attestaciyu as rzna on zna.id = rzna.zayavlenie_na_attestaciyu
                WHERE zna.vremya_provedeniya = :period AND zna.status = \'podpisano_ped_rabotnikom\' AND zna.rabota_dolzhnost in
                (
                    SELECT dak.dolzhnost FROM rabotnik_attestacionnoj_komissii as rak
                    INNER JOIN dolzhnost_attestacionnoj_komissii as dak on rak.attestacionnaya_komissiya = dak.attestacionnaya_komissiya
                    WHERE rak.fiz_lico = :fiz_lico
                )
                GROUP BY zna.id';
        $zayvleniya = [];
        $q = \Yii::$app->db->createCommand($sql)
                           ->bindValue(':period',$period)
                           ->bindValue(':fiz_lico',ApiGlobals::getFizLicoPolzovatelyaId())
                           ->queryAll();
        foreach ($q as $item) {
            $zayavlenie = new Zayavlenie();
            $zayavlenie->id = $item['id'];
            $zayavlenie->familiya = $item['familiya'];
            $zayavlenie->imya = $item['imya'];
            $zayavlenie->otchestvo = $item['otchestvo'];
            $zayavlenie->raspredelenie = $item['raspredelenie'] != null ? array_map('intval', explode(',',$item['raspredelenie'])) : [];
            $zayavlenie->raspredelenieCopy = $zayavlenie->raspredelenie;
            $sql = 'select alz.id, alz.rabotnik_komissii, alz.zayavlenie_na_attestaciyu,
                           sum(solz.bally) as bally, alz.nazvanie
                    from otsenochnyj_list_zayavleniya as alz
                    inner join struktura_otsenochnogo_lista_zayvaleniya as solz on alz.id = solz.otsenochnyj_list_zayavleniya
                    where alz.rabotnik_komissii = :fiz_lico and solz.uroven = 1
                          and alz.zayavlenie_na_attestaciyu = :zayavlenie
                    GROUP BY alz.id, alz.zayavlenie_na_attestaciyu, alz.rabotnik_komissii';
            $otsenki = [];
            $ostenkiData = \Yii::$app->db->createCommand($sql)
                ->bindValue(':zayavlenie', $item['id'])
                ->bindValue(':fiz_lico', ApiGlobals::getFizLicoPolzovatelyaId())
                ->queryAll();
            foreach ($ostenkiData as $item) {
                $otsenki[$item['rabotnik_komissii']][] = $item;
            }
            $zayavlenie->otsenki = $otsenki;

//                OtsenochnyjListZayavleniya::find()
//                ->joinWith('strukturaOtsenochnogoListaZayvaleniyaRel')
//                ->select([
//                    'otsenochnyj_list_zayavleniya.id',
//                    'otsenochnyj_list_zayavleniya.zayavlenie_na_attestaciyu',
//                    'sum(struktura_otsenochnogo_lista_zayvaleniya.bally) as bally'
//                ])
//                ->where(['otsenochnyj_list_zayavleniya.zayavlenie_na_attestaciyu'=>$item['id']])
//                ->andWhere(['struktura_otsenochnogo_lista_zayvaleniya.uroven'=>1])
//                ->groupBy([
//                    'otsenochnyj_list_zayavleniya.zayavlenie_na_attestaciyu',
//                    'otsenochnyj_list_zayavleniya.id'
//                ])
//                ->all();
            $zayvleniya[] = $zayavlenie;
        }

        return $zayvleniya;
    }

    public function actionGetRabotnikiKomissii(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $sql = 'SELECT rak.id,fl.familiya,fl.imya,fl.otchestvo,
                       fl.id as fiz_lico
                FROM rabotnik_attestacionnoj_komissii as rak
                INNER JOIN fiz_lico as fl on rak.fiz_lico = fl.id
                WHERE rak.attestacionnaya_komissiya in
                (
                  SELECT attestacionnaya_komissiya FROM rabotnik_attestacionnoj_komissii
                  WHERE fiz_lico = :fiz_lico
                )
                ORDER BY fl.familiya,fl.imya,fl.otchestvo';
        $rabotniki = [];
        $query = \Yii::$app->db->createCommand($sql)->bindValue(':fiz_lico',ApiGlobals::getFizLicoPolzovatelyaId())->queryAll();
        foreach ($query as $item) {
            $rabotnik = new RabotnikKomissii();
            $rabotnik->rabotnikId = $item['id'];
            $rabotnik->familiya = $item['familiya'];
            $rabotnik->imya = $item['imya'];
            $rabotnik->otchestvo = $item['otchestvo'];
            $rabotnik->fizLico = $item['fiz_lico'];
            $rabotniki[$rabotnik->rabotnikId] = $rabotnik;
        }
        return (array)$rabotniki;
    }

    public function actionSaveRaspredelenie(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $zayavleniya = \Yii::$app->request->post();
        $response = new JsResponse();
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($zayavleniya['zayavleniya'] as $item) {
                $zayavlenie = new Zayavlenie($item);
                $zayavlenie->saveRaspredelenie();
                $response->data[] = $zayavlenie;
            }
            $transaction->commit();
            $response->type = JsResponse::MSG_OPERATION_SUCCESS;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $response->type = JsResponse::MSG_OPERATION_ERROR;
            throw $e;
        }

        return $response;
    }

    public function actionResetBally(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $listId = \Yii::$app->request->post('id');

        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $list = OtsenochnyjListZayavleniya::findOne($listId);
            $list->status = StatusOtsenochnogoLista::REDAKTITUETSYA;
            $list->save();
            StrukturaOtsenochnogoListaZayvaleniya::updateAll(['bally'=>null],['otsenochnyj_list_zayavleniya'=>$listId]);
            $transaction->commit();
        }
        catch (Exception $e){
            $transaction->rollBack();
            $response->type = JsResponse::ERROR;
            $response->msg = 'Произошла ошибка при выполнении запроса к базе данных! '.$e->getMessage();
        }
        return $response;
    }

    public function accessRules()
    {
        return [
            '*' => Rol::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII,
        ];
    }
}