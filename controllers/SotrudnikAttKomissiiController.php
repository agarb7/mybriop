<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 27.03.16
 * Time: 11:19
 */

namespace app\controllers;


use app\components\Controller;
use app\components\JsResponse;
use app\entities\OtsenochnyjList;
use app\entities\OtsenochnyjListZayavleniya;
use app\entities\PostoyannoeIspytanie;
use app\entities\RabotnikAttestacionnojKomissii;
use app\entities\RaspredelenieZayavlenijNaAttestaciyu;
use app\entities\VremyaProvedeniyaAttestacii;
use app\entities\ZayavlenieNaAttestaciyu;
use app\enums\Rol;
use app\enums\StatusProgrammyKursa;
use app\enums\StatusZayavleniyaNaAttestaciyu;
use app\globals\ApiGlobals;
use yii\base\Exception;
use yii\web\Response;

class SotrudnikAttKomissiiController extends Controller
{
    public function actionIndex(){
        $periods = VremyaProvedeniyaAttestacii::find()->all();
        return $this->render('index.php',compact('periods'));
    }

    public function actionGetZayvleniya()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $fiz_lico = ApiGlobals::getFizLicoPolzovatelyaId();
        $periodId = \Yii::$app->request->post('period_id');
        $spisok = ZayavlenieNaAttestaciyu::find()
            ->joinWith('organizaciyaRel')
            ->joinWith('dolzhnostRel')
            ->joinWith('raspredelenieZayavlenijNaAttesctaciyuRel.rabotnikAttestacionnojKomissii')
            ->where(['zayavlenie_na_attestaciyu.status' => StatusZayavleniyaNaAttestaciyu::PODPISANO_PED_RABOTNIKOM])
            ->andWhere(['rabotnik_attestacionnoj_komissii.fiz_lico' => $fiz_lico])
            ->orderBy(
                'zayavlenie_na_attestaciyu.familiya,
                 zayavlenie_na_attestaciyu.imya,
                 zayavlenie_na_attestaciyu.otchestvo')
            ->asArray()
            ->all();
        $response->data = $spisok;
        return $response;
    }

    public function actionOtsenki(){
        $error = '';
        $fizLico = ApiGlobals::getFizLicoPolzovatelyaId();
        $zayavlenieId = $_REQUEST['zayavlenie_id'];
        $ajax = $_REQUEST['ajax'];
        $rabotnik = RabotnikAttestacionnojKomissii::find()->where(['fiz_lico'=>$fizLico])->one();
        $zayavlenie = ZayavlenieNaAttestaciyu::find()
            ->joinWith('portfolioFajlRel')
            ->where(['zayavlenie_na_attestaciyu.id'=>$zayavlenieId])
            ->one();
        $raspredelenie = RaspredelenieZayavlenijNaAttestaciyu::find()
            ->where(['rabotnik_attestacionnoj_komissii'=>$rabotnik->id])
            ->andWhere(['zayavlenie_na_attestaciyu'=>$zayavlenieId])
            ->exists();
        if ($raspredelenie){
            $transaction =  \Yii::$app->db->beginTransaction();
            try{
                $otsenochnieListy = OtsenochnyjList::find()
                    ->joinWith('ispytanieOtsenochnogoListaRel')
                    ->where(['ispytanie_otsenochnogo_lista.otsenochnyj_list'=>PostoyannoeIspytanie::getPortfolioId()])
                    ->all();
                foreach ($otsenochnieListy as $list) {
                    /**
                     * @var OtsenochnyjList $list
                     */
                    if (!OtsenochnyjListZayavleniya::find()
                        ->where(['otsenochnij_list'=>$list->id])
                        ->exists()) {
                        $new_ol_zayvaleniya = new OtsenochnyjListZayavleniya();
                        $new_ol_zayvaleniya->otsenochnijList = $list->id;
                        $new_ol_zayvaleniya->rabotnikKomissii = $fizLico;
                        $new_ol_zayvaleniya->zayavlenieNaAttestaciyu = $zayavlenieId;
                        $new_ol_zayvaleniya->postoyannoeIspytanie = PostoyannoeIspytanie::getPortfolioId();
                        $new_ol_zayvaleniya->nazvanie = $list->nazvanie;
                        $new_ol_zayvaleniya->minBallPervayaKategoriya = $list->minBallPervayaKategoriya;
                        $new_ol_zayvaleniya->minBallVisshayaKategoriya = $list->minBallVisshayaKategoriya;
                        $new_ol_zayvaleniya->save();
                        $sql = '
                          INSERT INTO struktura_otsenochnogo_lista_zayvaleniya
                          (otsenochnyj_list_zayavleniya,nazvanie,max_bally, nomer, uroven)
                          select :ol, sol.nazvanie, sol.bally,
                              case when sol.roditel is not null
                                then sol_roditel.nomer||\'.\'||sol.nomer
                                else cast(sol.nomer as varchar)
                              end as nomer,
                              case when sol.roditel is not null
                                then 2
                                else 1
                              end as uroven
                          from otsenochnyj_list as ol
                          inner join struktura_otsenochnogo_lista as sol on ol.id = sol.otsenochnyj_list
                          left join struktura_otsenochnogo_lista as sol_roditel on sol.roditel = sol_roditel.id
                          inner join ispytanie_otsenochnogo_lista as iol on ol.id = iol.otsenochnyj_list
                          where iol.postoyannoe_ispytanie = 1
                          order by nomer
                        ';
                        \Yii::$app->db->createCommand($sql)
                            ->bindValue(':ol', $new_ol_zayvaleniya->id)
                            ->execute();
                    }
                }
                $transaction->commit();
            }
            catch (Exception $e){
                $transaction->rollBack();
                $error = 'Оценочный лист не сформирован'.$e->getMessage();
            }
        }
        else{
            $error = 'Недоступное действие для данного пользователя';
        }
        $listy = OtsenochnyjListZayavleniya::find()
            ->joinWith('strukturaOtsenochnogoListaZayvaleniyaRel')
            ->where(['otsenochnyj_list_zayavleniya.rabotnik_komissii'=>$fizLico])
            ->andWhere(['otsenochnyj_list_zayavleniya.zayavlenie_na_attestaciyu'=>$zayavlenieId])
            ->all();
        $result = [];
        foreach ($listy as $list) {
            /**
             * @var OtsenochnyjListZayavleniya $list
             */
            if ($list->postoyannoeIspytanie == PostoyannoeIspytanie::getPortfolioId()){
                $result[] = [
                    'file_name' => $zayavlenie->portfolioFajlRel->vneshnee_imya_fajla,
                    'file_link' => $zayavlenie->portfolioFajlRel->getFileLink(),
                    'list' => $list->toArray(),
                    'struktura' => $list->strukturaOtsenochnogoListaZayvaleniyaRel
                ];
            }
        }

        if ($ajax){
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $response = new JsResponse();
            if ($error){
                $response->type = JsResponse::ERROR;
                $response->msg = $error;
            }
            else{
                $response->data = $result;
            }
            return $response;
        }
        else {
            return $this->render('otsenki.php');
        }
    }

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            '*' => '@'
        ];
    }
}