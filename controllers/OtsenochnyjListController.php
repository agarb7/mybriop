<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 22.02.16
 * Time: 20:17
 */

namespace app\controllers;


use app\components\Controller;
use app\components\JsResponse;
use app\entities\AttestacionnoeVariativnoeIspytanie_3;
use app\entities\IspytanieOtsenochnogoLista;
use app\entities\OtsenochnyjList;
use app\entities\PostoyannoeIspytanie;
use app\entities\StrukturaOtsenochnogoLista;
use app\enums\Rol;
use app\globals\ApiGlobals;
use yii\db\Expression;
use yii\db\Query;
use yii\web\Response;
use Yii;

class OtsenochnyjListController extends Controller
{
    public function actionIndex(){
        $var_ispyt_3 = [];
        foreach (AttestacionnoeVariativnoeIspytanie_3::find()
                     ->orderBy('nazvanie')
                     ->each() as $item) {
            $var_ispyt_3['var'.$item->id] = $item['nazvanie'];
        }
        $postoyannye = [];
        foreach (PostoyannoeIspytanie::find()
                     ->orderBy('nazvanie')
                 ->each() as $item) {
            $postoyannye['pos'.$item->id] = $item->nazvanie;
        }
        $ispytaniyaList = [
            'Постоянные испытания' => $postoyannye,
            'Третье вариативное испытание' => $var_ispyt_3
        ];
        return $this->render('index', compact('ispytaniyaList'));
    }

    public function actionGetOtsenochnyeListy()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $response->data = OtsenochnyjList::find()->asArray()->all();
        return $response;
    }

    public function actionAddOtsenochnyjList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $nazvanie = Yii::$app->request->post('nazvanie');
        $minBallPervayKategoriya = Yii::$app->request->post('minBallPervayKategoriya');
        $minBallVisshayaKategoriya = Yii::$app->request->post('minBallVisshayaKategoriya');
        $newOtsenochnyjList = new OtsenochnyjList();
        $newOtsenochnyjList->nazvanie = ApiGlobals::to_trimmed_text($nazvanie);
        $newOtsenochnyjList->minBallPervayaKategoriya = $minBallPervayKategoriya ? $minBallPervayKategoriya : null;
        $newOtsenochnyjList->minBallVisshayaKategoriya = $minBallVisshayaKategoriya ? $minBallVisshayaKategoriya : null;
        if ($newOtsenochnyjList->save()){
            $response->data = $newOtsenochnyjList;
        }
        else{
            $response->type = JsResponse::ERROR;
            $response->msg = JsResponse::MSG_OPERATION_ERROR;
        }
        return $response;
    }

    public function actionCommitEditList(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $id = Yii::$app->request->post('id');
        $newNazvanie = Yii::$app->request->post('new_nazvanie');
        $newMinBallPervayaKategoriya = Yii::$app->request->post('new_min_ball_pervaya_kategoriya');
        $newMinBallVisshayaKategoriya = Yii::$app->request->post('new_min_ball_visshay_kategoriya');
        $newMinBallVisshayaKategoriya = $newMinBallVisshayaKategoriya ? $newMinBallVisshayaKategoriya : null;
        $newMinBallPervayaKategoriya = $newMinBallPervayaKategoriya ? $newMinBallPervayaKategoriya : null;
        if ($newNazvanie == ''){
            $response->type = JsResponse::ERROR;
            $response->msg = 'Введите название';
        }
        else{
            /**
             * @var OtsenochnyjList $otsenochnyjList
             */
            $otsenochnyjList = OtsenochnyjList::findOne($id);
            $otsenochnyjList->nazvanie = ApiGlobals::to_trimmed_text($newNazvanie);
            $otsenochnyjList->minBallPervayaKategoriya = $newMinBallPervayaKategoriya;
            $otsenochnyjList->minBallVisshayaKategoriya = $newMinBallVisshayaKategoriya;
            if (!($otsenochnyjList->validate() and $otsenochnyjList->save())){
                $response->type = JsResponse::ERROR;
                $response->msg = 'Ошибка! Данные не сохранены';
            }
            else{
                $response->data = $otsenochnyjList->toArray();
            }
        }
        return $response;
    }

    public function actionDeleteOtsenochnyjList(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $id = Yii::$app->request->post('id');
        $otsenochnyjList = OtsenochnyjList::findOne($id);
        ;
        if (!($otsenochnyjList and $otsenochnyjList->delete()
            and StrukturaOtsenochnogoLista::deleteAll(['otsenochnyj_list'=>$id]) !== false))
        {
            $response->type = JsResponse::ERROR;
            $response->msg = JsResponse::MSG_OPERATION_ERROR;
        }
        return $response;
    }

    public function actionGetStruktura(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $list = Yii::$app->request->post('list');
        $response->data = StrukturaOtsenochnogoLista::find()
            ->joinWith('podstrukturaRel')
            ->where(['struktura_otsenochnogo_lista.otsenochnyj_list'=>$list])
            ->andWhere(['struktura_otsenochnogo_lista.roditel'=>null])
            ->orderBy([
                'struktura_otsenochnogo_lista.nomer'=>SORT_ASC,
                'podstruktura.nomer'=>SORT_ASC])
            ->asArray()
            ->all();
        return $response;
    }

    public function actionAddStruktura()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $nazvanie = Yii::$app->request->post('nazvanie');
        $bally = Yii::$app->request->post('bally');
        $nomer = Yii::$app->request->post('nomer');
        $roditel = Yii::$app->request->post('roditel');
        $otsenochnyjList = Yii::$app->request->post('otsenochnyj_list');
        if ($nazvanie == ''){
            $response->type = JsResponse::ERROR;
            $response->msg = 'Введите название';
        }
        if ($bally == '' or $bally < 0){
            $response->type = JsResponse::ERROR;
            $response->msg = 'Введите количество баллов, целое положительное число';
        }
        if ($response->type != JsResponse::ERROR){
            $newItem = new StrukturaOtsenochnogoLista();
            $newItem->nazvanie = $nazvanie;
            $newItem->bally =$bally;
            $newItem->nomer = $nomer;
            $newItem->roditel = $roditel;
            $newItem->otsenochnyjList = $otsenochnyjList;
            if ($newItem->validate() and $newItem->save()){
                if ($roditel == null)
                    $response->data = StrukturaOtsenochnogoLista::find()
                        ->joinWith('podstrukturaRel')
                        ->where(['struktura_otsenochnogo_lista.id'=>$newItem->id])
                        ->asArray()
                        ->one();
                else{
                    StrukturaOtsenochnogoLista::recalculateSummuBallov($roditel);
                    $response->data = StrukturaOtsenochnogoLista::find()
                        ->joinWith('podstrukturaRel')
                        ->where(['struktura_otsenochnogo_lista.id'=>$roditel])
                        ->orderBy([
                            'struktura_otsenochnogo_lista.nomer'=>SORT_ASC,
                            'podstruktura.nomer'=>SORT_ASC])
                        ->asArray()
                        ->one();
                }
            }
            else{
                $response->type = JsResponse::ERROR;
                $response->msg = 'Произошла ошибка во время сохранения данных!';
            }
        }
        return $response;
    }

    public function actionDeleteStruktura(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $id = Yii::$app->request->post('id');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $deletingItem = StrukturaOtsenochnogoLista::findOne($id);
            if (!is_null($deletingItem->roditel)){
                StrukturaOtsenochnogoLista::updateAll(
                    ['nomer'=> new Expression('nomer-1')],
                    'roditel='.$deletingItem->roditel.' and nomer>'.$deletingItem->nomer);

            }
            else{
                StrukturaOtsenochnogoLista::updateAll(
                    ['nomer'=>new Expression('nomer-1')],
                    'otsenochnyj_list='.$deletingItem->otsenochnyjList.' and nomer>'.$deletingItem->nomer);
                StrukturaOtsenochnogoLista::deleteAll(['roditel'=>$id]);
            }
            $roditel = $deletingItem->roditel;
            $deletingItem->delete();
            if (!is_null($roditel))
                StrukturaOtsenochnogoLista::recalculateSummuBallov($roditel);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $response->type = JsResponse::ERROR;
            $response->msg = JsResponse::MSG_OPERATION_ERROR;
            throw $e;
        }
        if (!is_null($roditel)){
            $response->data = StrukturaOtsenochnogoLista::find()
                ->joinWith('podstrukturaRel')
                ->where(['struktura_otsenochnogo_lista.id'=>$roditel])
                ->orderBy([
                    'struktura_otsenochnogo_lista.nomer'=>SORT_ASC,
                    'podstruktura.nomer'=>SORT_ASC])
                ->asArray()
                ->one();
        }

        return $response;
    }

    public function actionEditStruktura(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $id = Yii::$app->request->post('id');
        $nazvanie = Yii::$app->request->post('nazvanie');
        if (!$nazvanie){
            $response->type = JsResponse::ERROR;
            $response->msg = 'Введите название';
        }
        $bally = Yii::$app->request->post('bally');
        if (!$bally or $bally < 1){
            $response->type = JsResponse::ERROR;
            $response->msg = 'Введите количество баллов, целое положительно число';
        }
        if ($response->type != JsResponse::ERROR) {
            $editingItem = StrukturaOtsenochnogoLista::find()
                ->where(['id' => $id])
                ->one();
            $editingItem->nazvanie = $nazvanie;
            $editingItem->bally = $bally;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $editingItem->save();
                $id = -1;
                if ($editingItem->roditel) {
                    StrukturaOtsenochnogoLista::recalculateSummuBallov($editingItem->roditel);
                    $id = $editingItem->roditel;
                } else {
                    $id = $editingItem->id;
                }
                $transaction->commit();
                $response->data = StrukturaOtsenochnogoLista::find()
                    ->joinWith('podstrukturaRel')
                    ->where(['struktura_otsenochnogo_lista.id' => $id])
                    ->orderBy([
                        'struktura_otsenochnogo_lista.nomer'=>SORT_ASC,
                        'podstruktura.nomer'=>SORT_ASC])
                    ->asArray()
                    ->one();
            } catch (\Exception $e) {
                $transaction->rollBack();
                $response->type = JsResponse::ERROR;
                $response->msg = JsResponse::MSG_OPERATION_ERROR;
                throw $e;
            }
        }
        return $response;
    }

    public function actionGetIspytaniya(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $otsenochnyjList = Yii::$app->request->post('otsenochnyjList');
        $response->data = (new Query())
            ->select([
                'ispytanie_otsenochnogo_lista.id',
                'COALESCE(attestacionnoe_variativnoe_ispytanie_3.nazvanie,postoyannoe_ispytanie.nazvanie) as nazvanie'
            ])
            ->from('ispytanie_otsenochnogo_lista')
            ->leftJoin('postoyannoe_ispytanie','ispytanie_otsenochnogo_lista.postoyannoe_ispytanie = postoyannoe_ispytanie.id')
            ->leftJoin('attestacionnoe_variativnoe_ispytanie_3','ispytanie_otsenochnogo_lista.var_ispytanie_3 = attestacionnoe_variativnoe_ispytanie_3.id')
            ->where(['ispytanie_otsenochnogo_lista.otsenochnyj_list' => $otsenochnyjList])
            ->all();
        return $response;
    }

    public function actionAddIspytanie(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $ispytanie = Yii::$app->request->post('ispytanie');
        $otsenochnyjList = Yii::$app->request->post('otsenochnyjList');
        $type = substr($ispytanie,0,3);
        $id = substr($ispytanie,3,strlen($ispytanie)-1);
        $newItem = new IspytanieOtsenochnogoLista();
        $newItem->otsenochnyjList = $otsenochnyjList;
        $checkItem = false;
        if ($type == 'pos'){
            $newItem->postoyannoeIspytanie = $id;
            $newItem->varIspytanie_3 = null;
            $checkItem = IspytanieOtsenochnogoLista::find()
                ->where([
                    'otsenochnyj_list' => $otsenochnyjList,
                    'postoyannoe_ispytanie' => $id
                ])
                ->one();
        }
        else{
            $newItem->postoyannoeIspytanie = null;
            $newItem->varIspytanie_3 = $id;
            $checkItem = IspytanieOtsenochnogoLista::find()
                ->where([
                    'otsenochnyj_list' => $otsenochnyjList,
                    'var_ispytanie_3' => $id
                ])
                ->one();
        }
        if ($checkItem){
            $response->type = JsResponse::ERROR;
            $response->msg = 'Данное испытание уже присутствует в списке, выберите другое';
        }
        else {
            if ($newItem->save()) {
                $response->data = (new Query())
                    ->select([
                        'ispytanie_otsenochnogo_lista.id',
                        'COALESCE(attestacionnoe_variativnoe_ispytanie_3.nazvanie,postoyannoe_ispytanie.nazvanie) as nazvanie'
                    ])
                    ->from('ispytanie_otsenochnogo_lista')
                    ->leftJoin('postoyannoe_ispytanie', 'ispytanie_otsenochnogo_lista.postoyannoe_ispytanie = postoyannoe_ispytanie.id')
                    ->leftJoin('attestacionnoe_variativnoe_ispytanie_3', 'ispytanie_otsenochnogo_lista.var_ispytanie_3 = attestacionnoe_variativnoe_ispytanie_3.id')
                    ->where(['ispytanie_otsenochnogo_lista.id' => $newItem->id])
                    ->one();
            } else {
                $response->type = JsResponse::ERROR;
                $response->msg = JsResponse::MSG_OPERATION_ERROR;
            }
        }
        return $response;
    }

    public function actionDeleteIspytanie(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = new JsResponse();
        $id = Yii::$app->request->post('id');
        $ispytanie = IspytanieOtsenochnogoLista::findOne($id);
        if (!$ispytanie->delete()){
            $response->msg = JsResponse::MSG_OPERATION_ERROR;
            $response->type = JsResponse::ERROR;
        }
        return $response;
    }

    public function accessRules()
    {
        return [
            '*' => Rol::SOTRUDNIK_OTDELA_ATTESTACII
        ];
    }
}