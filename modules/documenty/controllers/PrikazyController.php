<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 22.05.2016
 * Time: 1:11
 */
namespace app\modules\documenty\controllers;

use app\entities\Kurs;
use app\globals\ApiGlobals;
use app\modules\documenty\models\DokPrikazTablica;
use app\modules\documenty\models\Prikaz;
use app\records\FizLico;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\modules\documenty\models\DokPrikazShablon;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class PrikazyController extends Controller
{
    /*** Создание приказа ***/
    public function actionSozdanie()
    {
        $user = \Yii::$app->user;
        if ($user->isGuest) return $this->redirect('/polzovatel/vhod');

        $post = \Yii::$app->request->post();
        if($post){
            $prikaz = new Prikaz();
            $prikaz->load(\Yii::$app->request->post());
            if ($prikaz->validate()){
                if($prikaz->save(false)){
                    \Yii::$app->session->setFlash('success','Данные  успешно сохранены!',false);
                    $this->redirect('/documenty/process/index');
                }else{
                    $messages[] = ['type'=>'danger','msg'=>'Данные не сохранены! Ошибка выполнения запроса к базе данных!'];
                    $shablony=DokPrikazShablon::find()->all();
                    return $this->render('prikaz', ['shablony' => $shablony, 'messages' => $messages]);
                };
            }else {
                $error = $prikaz->getErrors();
                $msg = 'Ошибка валидации данных:';
                if(array_key_exists('atributy',$error)) $msg .= ' '.$error['atributy'][0];
                if(array_key_exists('slushateli',$error)) $msg .= ' '.$error['slushateli'][0];
                if(array_key_exists('komissija',$error)) $msg .= ' '.$error['komissija'][0];
                $messages[] = ['type'=>'danger','msg'=>$msg];
                $shablony=DokPrikazShablon::find()->all();
                return $this->render('prikaz', ['shablony' => $shablony, 'messages' => $messages]);
            }
        }elseif (Yii::$app->request->isAjax && $tip = Yii::$app->request->get('tip')){
            $prikaz = new Prikaz();
            $prikaz->shablonId = $tip;
            $prikaz->avtorId = ApiGlobals::getPolzovatelId();
            $prikaz->dataSozdanija = date("Y-m-d");
            $query = DokPrikazShablon::find()->with('atributyRel')->where(['id'=>$tip])->asArray()->one();
            $prikaz->atributy = ArrayHelper::map($query['atributyRel'],'id','');
            $nazvanija = ArrayHelper::getColumn($query['atributyRel'], 'nazvanie_tekst');
            echo $this->renderAjax($prikaz->getShablonFileName($tip), [
                'prikaz' => $prikaz, 'nazvanija' => $nazvanija,
            ]);
            Yii::$app->end();
        }else{
            $shablony=DokPrikazShablon::find()->all();
            $messages =[];
            if($shablony===null){
                throw new NotFoundHttpException;
            };
            return $this->render('prikaz', ['shablony' => $shablony, 'messages' => $messages]);
        }
    }
    
    /*** Просмотр приказов ***/
    public function actionView($pid)
    {
        $prikaz = new Prikaz($pid);
        if($prikaz===null)throw new NotFoundHttpException;
        elseif($prikaz->shablonId == 1){
            $kursId =  $prikaz['atributy']['2'];
            $kurs = Kurs::findOne(['id' => $kursId]);
            $nazvanie = $kurs->nazvanie;
            $slushateli = $prikaz->getSlushateliPrikaza($pid);
            $komissija = $prikaz->getKomissija($pid);
            $avtor = $prikaz->getAvtor($prikaz->avtorId);
            return $this->render('_zachislenie-view.php', compact('prikaz','nazvanie','slushateli','komissija','avtor'));
        }
    }

    /*** Редактирование приказа ***/
    public function actionEdit($pid)
    {
        $user = \Yii::$app->user;
        if ($user->isGuest) return $this->redirect('/polzovatel/vhod');

        $post = \Yii::$app->request->post();
        if($post){
            $pid = ArrayHelper::getValue($post['Prikaz'], 'id');
            $prikaz = new Prikaz($pid);
            if ($prikaz->shablonId == 1) {
                $nsl = $post['Prikaz']['slushateli']; // новый список слушателей
                $nk = $post['Prikaz']['komissija']; // новый состав комиссии
                $ot = DokPrikazTablica::find()->where(['prikaz_id' => $pid])->all();
                $ot_as_array = ArrayHelper::toArray($ot);
                $osl = array_filter(ArrayHelper::getColumn($ot_as_array, 'kurs_fiz_lica_id')); // сохраненный список слушателей
                $ok = array_filter(ArrayHelper::getColumn($ot_as_array, 'fiz_lico_id')); // сохраненный состав комиссии
                $e = false;
                $transaction = \Yii::$app->db->beginTransaction();
                foreach ($nsl as $nv){
                    if (!in_array($nv, $osl)){// добавлен новый слушатель в приказ
                        $dpt = new DokPrikazTablica();
                        $dpt->prikaz_id = $pid;
                        $dpt->kurs_fiz_lica_id = $nv;
                        if (!$dpt->save()) $e = true;
                    }
                }
                foreach ($osl as $ov){
                    if (!in_array($ov, $nsl)){// удален слушатель из приказа
                        $dpt = DokPrikazTablica::findOne(['kurs_fiz_lica_id' => $ov]);
                        if (!$dpt->delete()) $e = true;
                    }
                }
                reset($ok);
                reset($nk);
                for ($i=0; $i<3; $i++){
                    if (current($ok) <> current($nk)){
                        $dpt = DokPrikazTablica::findOne(['fiz_lico_id' => current($ok), 'prikaz_id' => $pid]);
                        $dpt->fiz_lico_id = current($nk);
                        if (!$dpt->save()) $e = true;
                    }
                    next($ok);
                    next($nk);
                }
                if (!$e) {
                    $transaction->commit();
                    \Yii::$app->session->setFlash('success','Данные успешно обновлены!',false);
                    $this->redirect('/documenty/process/index');
                }else{
                    $transaction->rollback();
                    \Yii::$app->session->setFlash('danger','Данные не обновлены!',false);
                    $this->redirect('/documenty/process/index');
                }
            }
        }else{
            $prikaz = new Prikaz($pid);
            if($prikaz===null)throw new NotFoundHttpException;
            elseif($prikaz->shablonId == 1){
                $kursId =  $prikaz['atributy']['2'];
                $kurs = Kurs::findOne(['id' => $kursId]);
                $nazvanie = $kurs->nazvanie;
                $slushateli = $prikaz->getSlushateliKursa($kursId);
                $komissija = [];
                foreach (FizLico::find()->asArray()->orderBy('id')->all() as $v){
                    $komissija[$v['id']] = implode(' ',array($v['familiya'],$v['imya'],$v['otchestvo']));
                };
                $avtor = $prikaz->getAvtor($prikaz->avtorId);
                $sprovider = new ArrayDataProvider([
                    'allModels' => $slushateli,
                    'pagination' => false,
                ]);
                return $this->render('_zachislenie-edit.php', compact('prikaz','nazvanie','avtor','sprovider','komissija'));
            }
        }
    }
    
    /*** Приказ о зачислении ***/
    public function actionZachislenie()
    {
        if (Yii::$app->request->isAjax && $god = Yii::$app->request->get('god')){
            $user=Yii::$app->user->fizLico->id;
            $data=Kurs::findAll(['plan_prospekt_god' => $god.'-01-01', 'rukovoditel' => $user,]);
            echo "<option value=''>выберите программу</option>";
            foreach (ArrayHelper::toArray($data) as $item) {
                echo "<option value='" . $item['id'] . "'>" . $item['nazvanie']. "</option>";
            }
        }elseif(Yii::$app->request->isAjax && $kurs = Yii::$app->request->get('kurs')){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $query=Kurs::findOne(['id' => $kurs]);
            $rekvizity = ['kategorija' => implode(", ", ArrayHelper::getColumn($query->kategoriiSlushatelejRel, 'nazvanie', false)),
                'chasy' => $query->raschitanoChasov,
                'nachalo' => Yii::$app->formatter->asDate($query->nachaloAsDate),
                'konec' => Yii::$app->formatter->asDate($query->konecAsDate)];

            $response=$rekvizity;
            echo Json::encode($response); die();
        }
    }

    public function actionZachislenieTablica()
    {
        if(Yii::$app->request->isAjax && $kurs = Yii::$app->request->get('kurs')) {
            $this->layout = false;
            $prikaz = new Prikaz();
            $data = $prikaz->getSlushateliKursa($kurs);
            $sotrudniki = $prikaz->getSotrudniki();
            $komissija = [];
            foreach (FizLico::find()->asArray()->orderBy('id')->all() as $v){
                $komissija[$v['id']] = implode(' ',array($v['familiya'],$v['imya'],$v['otchestvo']));
            };
            if (!empty($data)) {
                $provider = new ArrayDataProvider([
                    'allModels' => $data,
                    'pagination' => false,
                ]);
                echo $this->renderAjax('_zachislenie-tablica', ['provider' => $provider, 'komissija' => $komissija]);
            }else{
                echo 'На курс еще никто не записан!';
            }
        }
    }
}