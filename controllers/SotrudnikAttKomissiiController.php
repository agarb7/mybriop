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
use app\entities\AttestacionnoeVariativnoeIspytanie_3;
use app\entities\OtsenochnyjList;
use app\entities\OtsenochnyjListZayavleniya;
use app\entities\PostoyannoeIspytanie;
use app\entities\RabotnikAttestacionnojKomissii;
use app\entities\RaspredelenieZayavlenijNaAttestaciyu;
use app\entities\StrukturaOtsenochnogoListaZayvaleniya;
use app\entities\VremyaProvedeniyaAttestacii;
use app\entities\ZayavlenieNaAttestaciyu;
use app\enums\KategoriyaPedRabotnika;
use app\enums\Rol;
use app\enums\StatusProgrammyKursa;
use app\enums\StatusZayavleniyaNaAttestaciyu;
use app\enums2\StatusOtsenochnogoLista;
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
            ->joinWith('raspredelenieZayavlenijNaAttesctaciyuRel.rabotnikAttestacionnojKomissiiRel')
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
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $error = '';
        $fizLico = ApiGlobals::getFizLicoPolzovatelyaId();
        $zayavlenieId = $_REQUEST['zayavlenie_id'];
        $ajax = $_REQUEST['ajax'];
        $rabotnik = RabotnikAttestacionnojKomissii::find()->where(['fiz_lico'=>$fizLico])->one();
        /**
         * @var ZayavlenieNaAttestaciyu $zayavlenie
         */
        $zayavlenie = ZayavlenieNaAttestaciyu::find()
            ->joinWith('portfolioFajlRel')
            ->joinWith('varIspytanie3FajlRel')
            ->joinWith('prezentatsiyaFajlRel')
            ->joinWith('otraslevoeSoglashenieZayavleniyaRel')
            ->where(['zayavlenie_na_attestaciyu.id'=>$zayavlenieId])
            ->one();
        $raspredelenie = RaspredelenieZayavlenijNaAttestaciyu::find()
            ->where(['rabotnik_attestacionnoj_komissii'=>$rabotnik->id])
            ->andWhere(['zayavlenie_na_attestaciyu'=>$zayavlenieId])
            ->exists();
        $first = function($array){
            if (count($array) > 0){
                return $array[0];
            }
            else{
                return false;
            }
        };
        if ($raspredelenie){
            $transaction =  \Yii::$app->db->beginTransaction();
            try{
                $postoyannieIspyetaniya = [PostoyannoeIspytanie::getPortfolioId()];
                $variativnoeIspytanie = [];
                if ($zayavlenie->na_kategoriyu == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA){
                    $postoyannieIspyetaniya[] = PostoyannoeIspytanie::getSpdId();
                    if (count($zayavlenie->otraslevoeSoglashenieZayavleniyaRel) == 0)
                        $variativnoeIspytanie[] = $zayavlenie->var_ispytanie_3;
                }
                $otsenochnieListy = OtsenochnyjList::find()
                    ->joinWith('ispytanieOtsenochnogoListaRel')
                    ->where(['in','ispytanie_otsenochnogo_lista.postoyannoe_ispytanie',$postoyannieIspyetaniya])
                    ->orWhere(['in','ispytanie_otsenochnogo_lista.var_ispytanie_3',$variativnoeIspytanie])
                    ->all();
                foreach ($otsenochnieListy as $list) {
                    /**
                     * @var OtsenochnyjList $list
                     */
                    if (!OtsenochnyjListZayavleniya::find()
                        ->where(['otsenochnij_list'=>$list->id])
                        ->andWhere(['rabotnik_komissii'=>$fizLico])
                        ->andWhere(['zayavlenie_na_attestaciyu'=>$zayavlenieId])
                        ->exists()) {
                        $new_ol_zayvaleniya = new OtsenochnyjListZayavleniya();
                        $new_ol_zayvaleniya->otsenochnijList = $list->id;
                        $new_ol_zayvaleniya->rabotnikKomissii = $fizLico;
                        $new_ol_zayvaleniya->zayavlenieNaAttestaciyu = $zayavlenieId;
                        $ispytanie = $first($list->ispytanieOtsenochnogoListaRel);
                        if ($ispytanie && $ispytanie->postoyannoeIspytanie)
                            $new_ol_zayvaleniya->postoyannoeIspytanie = $ispytanie->postoyannoeIspytanie;
                        if ($ispytanie && $ispytanie->var_ispytanie_3)
                            $new_ol_zayvaleniya->var_ispytanie_3 = $ispytanie->var_ispytanie_3;
                        $new_ol_zayvaleniya->nazvanie = $list->nazvanie;
                        $new_ol_zayvaleniya->minBallPervayaKategoriya = $list->minBallPervayaKategoriya;
                        $new_ol_zayvaleniya->minBallVisshayaKategoriya = $list->minBallVisshayaKategoriya;

                        $new_ol_zayvaleniya->save();
                        $sql = '
                          INSERT INTO struktura_otsenochnogo_lista_zayvaleniya
                          (otsenochnyj_list_zayavleniya,nazvanie,max_bally, nomer, uroven, struktura_otsenochnogo_lista, roditel)
                          select :ol, sol.nazvanie, sol.bally,
                              case when sol.roditel is not null
                                then sol_roditel.nomer||\'.\'||sol.nomer
                                else cast(sol.nomer as varchar)
                              end as nomer,
                              case when sol.roditel is not null
                                then 2
                                else 1
                              end as uroven,
                              sol.id, sol.roditel
                          from otsenochnyj_list as ol
                          inner join struktura_otsenochnogo_lista as sol on ol.id = sol.otsenochnyj_list
                          left join struktura_otsenochnogo_lista as sol_roditel on sol.roditel = sol_roditel.id
                          inner join ispytanie_otsenochnogo_lista as iol on ol.id = iol.otsenochnyj_list
                          where ol.id = '.$list->id.' and '.
                            ($ispytanie->var_ispytanie_3 ? 'iol.var_ispytanie_3=:isp' : 'iol.postoyannoe_ispytanie=:isp').'
                          order by nomer
                        ';
                        \Yii::$app->db->createCommand($sql)
                            ->bindValue(':ol', $new_ol_zayvaleniya->id)
                            ->bindValue(':isp', $ispytanie->var_ispytanie_3 ? $ispytanie->var_ispytanie_3 : $ispytanie->postoyannoeIspytanie)
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
                //$result[] =$list->status;
                $portfolio = PostoyannoeIspytanie::find()->where(['id'=>PostoyannoeIspytanie::getPortfolioId()])->one();
                $result[] = new \app\models\sotrudnik_att_komissii\OtsenochnyjList([
                    'ispytanie_name' => $portfolio->nazvanie,
                    'file_name' => $zayavlenie->portfolioFajlRel ? $zayavlenie->portfolioFajlRel->vneshnee_imya_fajla : '',
                    'file_link' => $zayavlenie->portfolioFajlRel ? $zayavlenie->portfolioFajlRel->getUri() : '',
                    'list' => $list,
                    'struktura' => $list->strukturaOtsenochnogoListaZayvaleniyaRel
                ]);
            }
            else if ($list->postoyannoeIspytanie == PostoyannoeIspytanie::getSpdId()){
                //$result[] =$list->status;
                $spd = PostoyannoeIspytanie::find()->where(['id'=>PostoyannoeIspytanie::getSpdId()])->one();
                $result[] = new \app\models\sotrudnik_att_komissii\OtsenochnyjList([
                    'ispytanie_name' => $spd->nazvanie,
                    'file_name' => $zayavlenie->prezentatsiyaFajlRel ? $zayavlenie->prezentatsiyaFajlRel->vneshnee_imya_fajla : '',
                    'file_link' => $zayavlenie->prezentatsiyaFajlRel ? $zayavlenie->prezentatsiyaFajlRel->getUri() : '',
                    'list' => $list,
                    'struktura' => $list->strukturaOtsenochnogoListaZayvaleniyaRel
                ]);
            }
            else if ($list->varIspytanie_3){
                //$result[] =$list->status;
                $varIspytanie3 = AttestacionnoeVariativnoeIspytanie_3::find()->where(['id'=>$list->varIspytanie_3])->one();
                $result[] = new \app\models\sotrudnik_att_komissii\OtsenochnyjList([
                    'ispytanie_name' => $varIspytanie3->nazvanie,
                    'file_name' => $zayavlenie->varIspytanie3FajlRel ? $zayavlenie->varIspytanie3FajlRel->vneshnee_imya_fajla : '',
                    'file_link' => $zayavlenie->varIspytanie3FajlRel ? $zayavlenie->varIspytanie3FajlRel->getUri() : '',
                    'list' => $list,
                    'struktura' => $list->strukturaOtsenochnogoListaZayvaleniyaRel
                ]);
            }
        }

        if ($error){
            $response->type = JsResponse::ERROR;
            $response->msg = $error;
        }
        else{
            $response->data = $result;
        }
        return $response;
    }

    public function actionSaveOtsenki(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $list = \Yii::$app->request->post('list');
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($list['struktura'] as $item) {
                $struktura = StrukturaOtsenochnogoListaZayvaleniya::findOne($item['id']);
                $struktura->bally = $item['bally'];
                $struktura->save();
            }
            $list = OtsenochnyjListZayavleniya::findOne($list['id']);
            $list->status = StatusOtsenochnogoLista::ZAPOLNENO;
            $list->save();
            $transaction->commit();
            $response->data = StatusOtsenochnogoLista::ZAPOLNENO;
        }
        catch (Exception $e){
            $transaction->rollBack();
            $response->type = JsResponse::ERROR;
            $response->msg = 'Данные не сохранены';
        }
        return $response;
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