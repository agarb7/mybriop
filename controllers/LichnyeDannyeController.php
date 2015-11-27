<?php
namespace app\controllers;

use app\components\Controller;
use app\models\lichnye_dannye\ObschieDannyeForm;
use Yii;

class LichnyeDannyeController extends Controller
{
    public function actionIndex()
    {
        $post = Yii::$app->request->post();

        $model = new ObschieDannyeForm;

        if ($model->load($post))
            $model->save();
        else
            $model->populate();

        return $this->render('index', compact('model'));
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