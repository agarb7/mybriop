<?php
namespace app\controllers;

use app\components\captcha\CaptchaAction;
use app\enums\KategoriyaPedRabotnika;
use app\models\entities\FizLico;
use app\models\TestModel;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\User;

class SiteController extends Controller
{
    public function actionTest()
    {
        $post = \Yii::$app->request->post();
        $model = new FizLico;
        $model->familiya = 'Gorbachev';

        if ($model->load($post) && $model->save())
            return 'ok';

        return $this->render('test', compact('model'));
    }

    public function actionIndex()
    {
        if (!\Yii::$app->user->isGuest)
            return $this->render('index');
        else
            return $this->redirect('/polzovatel/vhod');
    }

    public function actions()
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::className(),
                'minLength' => 3,
                'maxLength' => 4,
                'offset' => 2,
                'width' => 180,
                'transparent' => true
            ]
        ];
    }
}
