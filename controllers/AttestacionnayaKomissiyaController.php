<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 22.11.15
 * Time: 16:25
 */

namespace app\controllers;


use app\entities\AttestatsionnayaKomissiya;
use yii\web\Controller;

class AttestacionnayaKomissiyaController extends Controller
{
    public function actionIndex()
    {
        $komissii = AttestatsionnayaKomissiya::find()->orderBy('nazvanie')->all();
        return $this->render('index',compact('komissii'));
    }

    public function actionGetKomissii(){

    }
}