<?php
namespace app\controllers;

use app\components\Controller;
use app\models\lichnye_dannye_obschie\ObschieDannyeForm;
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
}