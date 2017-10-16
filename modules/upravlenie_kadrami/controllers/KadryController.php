<?php

namespace app\modules\upravlenie_kadrami\controllers;

use Yii;
use yii\web\Controller;
use app\entities\EntityQuery;
use app\entities\Organizaciya;
use app\modules\upravlenie_kadrami\models\Registraciya;
use yii\web\Response;

class KadryController extends Controller
{
    public function actionRegistraciya()
    {
        $post = Yii::$app->request->post();
        $model = new Registraciya;
        if ($model->load($post) && $model->register())
            return $this->render('registraciya-zakonchena', ['model' => $model]);
        $model->setDefaults();
        return $this->render('index', ['model' => $model]);
    }

    public function actionRabotaOrg()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $parents = Yii::$app->request->post('depdrop_parents');

        $vedomstvo_id = $parents[0];
        $ao_id = $parents[1];
        $params['valueColumn'] = 'nazvanie';

        return Organizaciya::findByVedomstvoAndAdres($vedomstvo_id, $ao_id)
            ->commonOnly()
            ->formattedAll(EntityQuery::DEP_DROP_AJAX, $params);
    }

    public function accessRules()
    {
        return [
            '*' => Rol::ADMINISTRATOR,
            'rabota-org' => '?',
        ];
    }
}
