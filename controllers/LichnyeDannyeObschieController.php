<?php
namespace app\controllers;

use app\components\Controller;
use app\models\lichnye_dannye_obschie\ObschieDannyeForm;
use app\models\lichnye_dannye_obschie\PasswordForm;
use Yii;

class LichnyeDannyeObschieController extends Controller
{
    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            '*' => '@'
        ];
    }

    public function actionIndex()
    {
        $model = ObschieDannyeForm::findOne(Yii::$app->user->fizLicoId);

        if ($model->load(Yii::$app->request->post()))
            $model->save();

        return $this->render('index', compact('model'));
    }

    public function actionPassword()
    {
        /* @var $model PasswordForm */
        $model = PasswordForm::findIdentity(Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()))
            $model->save();

        return $this->render('password', compact('model'));
    }
}