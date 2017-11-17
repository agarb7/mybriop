<?php

namespace app\modules\upravlenie_kadrami\controllers;

use app\entities\Polzovatel;
use app\records\FizLico;
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
        $fl = '';
        if ($model->load($post) && $model->register()) {
            if ($model->fizLicoId) $fl = FizLico::findOne(['id' => $model->fizLicoId]);
            return $this->render('registraciya-zakonchena', ['model' => $model, 'fizlico' => $fl]);
        }
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
    
    public function actionPerson()
    {
        if (Yii::$app->request->isAjax && $id = Yii::$app->request->get('id')){
            $fl = FizLico::findOne(['id' => $id]);
            $p = Polzovatel::findOne(['fiz_lico' => $id]);
            echo $this->renderAjax('person.php', ['fizlico' => $fl, 'polzovatel' =>$p]);
        }
    }
}
