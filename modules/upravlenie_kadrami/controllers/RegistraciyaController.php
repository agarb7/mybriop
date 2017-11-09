<?php

namespace app\modules\upravlenie_kadrami\controllers;

use Yii;
use yii\web\Controller;
use app\entities\EntityQuery;
use app\entities\Organizaciya;
use app\modules\upravlenie_kadrami\models\Sotrudnik;
use yii\web\Response;
use yii\filters\AccessControl;
use app\enums2\Rol;

class RegistraciyaController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        // all the action are accessible to ADMINISTRATOR, SOTRUDNIK_UCHEBNOGO_OTDELA and SOTRUDNIK_OTDELA_KADROV
                        'allow' => true,
                        'roles' => [
                            Rol::ADMINISTRATOR,
                            Rol::SOTRUDNIK_UCHEBNOGO_OTDELA,
                            Rol::SOTRUDNIK_OTDELA_KADROV,
                        ]
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $post = Yii::$app->request->post();
        $model = new Sotrudnik();
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
}
