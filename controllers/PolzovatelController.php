<?php

namespace app\controllers;

use app\components\Controller;
use app\entities\EntityQuery;
use app\entities\Organizaciya;
use app\models\polzovatel\PodtverzhdenieEmail;
use app\models\polzovatel\Registraciya;
use app\models\polzovatel\Vhod;
use yii\web\Response;
use Yii;

class PolzovatelController extends Controller
{
    public function actionVhod()
    {
        $post = \Yii::$app->request->post();
        $model = new Vhod;

        if ($model->load($post) && $model->login())
            return $this->redirect('/');

        $this->layout = 'polzovatel/vhod';
        return $this->render('vhod', compact('model'));
    }

    public function actionVyhod()
    {
        Yii::$app->user->logout();
        return $this->redirect('/');
    }

    public function actionRegistraciya()
    {
        $post = Yii::$app->request->post();

        $model = new Registraciya;
        if ($model->load($post) && $model->register())
            return $this->render('registraciya-zakonchena', compact('model'));

        $this->layout = 'polzovatel/registraciya';
        $model->setDefaultsIfEmpty();
        return $this->render('registraciya', compact('model'));
    }

    public function actionPodtverzhdenieEmail()
    {
        $get = Yii::$app->request->get();

        $model = new PodtverzhdenieEmail;
        if ($model->load($get, ''))
            $model->activatePolzovatel();

        return $this->render('podtverzhdenie-email', compact('model'));
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

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            'vhod' => '?',
            'vyhod' => '@',
            'registraciya' => '?',
            'podtverzhdenie-email' => '?',
            'rabota-org' => '?'
        ];
    }

}
