<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 22.05.2016
 * Time: 1:11
 */
namespace app\modules\documenty\controllers;

use app\modules\documenty\models\Dok;
use app\modules\documenty\models\DokPrikaz;
use app\modules\documenty\models\DokProcess;
use app\modules\documenty\models\Prikaz;
use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;

class ProcessController extends Controller
{
    /*** Загрузка документов, создание новых  ***/
    public function actionIndex()
    {
        $user = \Yii::$app->user;
        if ($user->isGuest) return $this->redirect('/polzovatel/vhod');
              
        $p = new DokProcess();
        $process = new ArrayDataProvider([
            'allModels' => $p->getProcess(),
            'pagination' => false,
        ]);
        $prikazy = new DokPrikaz();
        $zp = new ArrayDataProvider([
            'allModels' => $prikazy->getZaregistrirovannyePrikazy(),
        ]);
        return $this->render('index', ['process' => $process, 'zp'=>$zp]);
    }

    public function actionPodpisanie()
    {
        $procid = $_REQUEST['procid'];
        if ($_REQUEST['comment'] == '') {
            $comment = null;
        }else{
            $comment =  preg_replace("/(\s){2,}/",' ',trim($_REQUEST['comment']));
        }
        $u = \Yii::$app->user->identity;
        $error = false;
        $answer = [];

        // завершение текущего процесса
        $current = DokProcess::findOne(['id' => $procid]);
        $cp = new DokProcess();
        $cp->dok_id = $current['dok_id'];
        $cp->roli_id = $current['roli_id'];
        $cp->ispolnil_fiz_lico_id = $u->fiz_lico;
        $cp->porjadok = $current['porjadok'];
        $cp->nazvanie = $current['nazvanie'];
        $cp->komentarij = $comment;
        $cp->data_zavershenija = date('Y-m-d');

        $transaction = \Yii::$app->db->beginTransaction();

        // если документ процесса приказ, то запретить редактирование
        $cdok = $cp->getProcess();
        if ($cdok[$cp->dok_id]['dok_tip'] == 'Приказ' and $cp->porjadok == 1){
            $pid = $cdok[$cp->dok_id]['pid'];
            $p = DokPrikaz::findOne(['id' => $pid]);
            $p->redaktiruetsja = 0;
            if (!$p->save()) $error = true;
        }

        // создание нового процесса
        $np = new DokProcess();
        $new = $np->getNextProcess($procid);
        $np->dok_id = $new['dok_id'];
        $np->roli_id = $new['roli_id'];
        $np->sozdal_fiz_lico_id = $u->fiz_lico;
        $np->porjadok = $new['porjadok'];
        $np->nazvanie = $new['opisanie'];
        $np->komentarij = $comment;
        $np->data_vnesenija = date('Y-m-d');

        if (!$cp->save() or !$np->save()) $error = true;
        if (!$error) {
            $transaction->commit();
            $answer['result'] = 'success';
        }else{
            $transaction->rollback();
        }
        \Yii::$app->response->format = 'json';
        return $answer;
    }

    public function actionVozvrat()
    {
        $procid = $_REQUEST['procid'];
        if ($_REQUEST['comment'] == '') {
            $comment = null;
        }else{
            $comment =  preg_replace("/(\s){2,}/",' ',trim($_REQUEST['comment']));
        }
        $u = \Yii::$app->user->identity;
        $error = false;
        $answer = [];

        // завершение текущего процесса
        $current = DokProcess::findOne(['id' => $procid]);
        $cp = new DokProcess();
        $cp->dok_id = $current['dok_id'];
        $cp->roli_id = $current['roli_id'];
        $cp->ispolnil_fiz_lico_id = $u->fiz_lico;
        $cp->porjadok = $current['porjadok'];
        $cp->nazvanie = $current['nazvanie'];
        $cp->komentarij = $comment;
        $cp->data_zavershenija = date('Y-m-d');
        $cp->vernut_avtoru = true;

        $transaction = \Yii::$app->db->beginTransaction();

        // если документ процесса приказ, то разрешить редактирование
        $cdok = $cp->getProcess();
        if ($cdok[$cp->dok_id]['dok_tip'] == 'Приказ'){
            $pid = $cdok[$cp->dok_id]['pid'];
            $p = DokPrikaz::findOne(['id' => $pid]);
            $p->redaktiruetsja = 1;
            if (!$p->save()) $error = true;
        }

        // coздание нового процесса на редактирование
        $np = new DokProcess();
        $fp = DokProcess::find()->where(['dok_id' => $current['dok_id']])->andWhere(['porjadok' => 1])->one();
        //var_dump($fp);die();
        $np->dok_id = $current['dok_id'];
        $np->roli_id = $fp['roli_id'];
        $np->sozdal_fiz_lico_id = $u->fiz_lico;
        $np->porjadok = $fp['porjadok'];
        $np->nazvanie = $fp['nazvanie'];
        $np->komentarij = $comment;
        $np->data_vnesenija = date('Y-m-d');

        if (!$cp->save() or !$np->save()) $error = true;
        if (!$error) {
            $transaction->commit();
            $answer['result'] = 'success';
        }else{
            $transaction->rollback();
        }
        \Yii::$app->response->format = 'json';
        return $answer;
    }
    
    public function actionRegistracija()
    {
        $procid = $_REQUEST['procid'];
        $nomer = $_REQUEST['nomer'];
        $datereg = $_REQUEST['datereg'];

        $u = \Yii::$app->user->identity;
        $error = false;
        $answer = [];
        
        // завершение текущего процесса
        $current = DokProcess::findOne(['id' => $procid]);
        $cp = new DokProcess();
        $cp->dok_id = $current['dok_id'];
        $cp->roli_id = $current['roli_id'];
        $cp->ispolnil_fiz_lico_id = $u->fiz_lico;
        $cp->porjadok = $current['porjadok'];
        $cp->nazvanie = $current['nazvanie'];
        $cp->data_zavershenija = date('Y-m-d');

        $transaction = \Yii::$app->db->beginTransaction();
        if (!$cp->save()) $error = true;

        // регистрация приказа
        $cdok = $cp->getProcess();
        if ($cdok[$cp->dok_id]['dok_tip'] == 'Приказ'){
            $pid = $cdok[$cp->dok_id]['pid'];
            $p = DokPrikaz::findOne(['id' => $pid]);
            $p->nomer_registracii = $nomer;
            $p->data_registracii = date('Y-m-d', strtotime($datereg));
            $p->status_podpisan = 1;
            if (!$p->save()) $error = true;
        }

        if (!$error) {
            $transaction->commit();
            $answer['result'] = 'success';
        }else{
            $transaction->rollback();
        }
        \Yii::$app->response->format = 'json';
        return $answer;
    }

    public function actionUdalenie()
    {
        $dokid = $_REQUEST['dokid'];
        $answer = [];

        $dok = Dok::findOne(['dok.id' => $dokid]);
        $dok->actual = false;

        if ($dok->save()) $answer['result'] = 'success';

        \Yii::$app->response->format = 'json';
        return $answer;
    }
}