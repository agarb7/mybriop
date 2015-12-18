<?php
namespace app\controllers;

use app\components\Controller;
use app\entities\ObrazovanieFizLica;
use app\models\lichnye_dannye\ObrazovanieForm;
use app\models\lichnye_dannye\ObschieDannyeForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

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

    public function actionObrazovaniya()
    {
        $query = ObrazovanieFizLica::find()
            ->joinWith('organizaciyaRel')
            ->joinWith('kvalifikaciyaRel')
            ->hasFizLico();

        $provider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'hashids',
            'pagination' => false,
            'sort' => false
        ]);

        return $this->render('obrazovaniya', ['provider' => $provider]);
    }

    public function actionCreateObrazovanie()
    {
        $post = Yii::$app->request->post();

        $model = new ObrazovanieForm([
            'fizLicoId' => Yii::$app->user->fizLicoId
        ]);

        if ($model->load($post) && $model->save())
            return $this->redirect(['obrazovaniya']);

        return $this->render('create-obrazovanie', ['model' => $model]);
    }

    public function actionDeleteObrazovanie($id)
    {
        $model = new ObrazovanieForm(['id' => $id]);

        if ($model->delete())
            return $this->redirect(['obrazovaniya']);

        throw new NotFoundHttpException;
    }

    public function actionUpdateObrazovanie($id)
    {
        $model = new ObrazovanieForm(['id' => $id]);

        if (!$model->populate())
            throw new NotFoundHttpException;

        if ($model->fizLicoId !== Yii::$app->user->fizLicoId)
            throw new ForbiddenHttpException;

        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->save())
            return $this->redirect(['obrazovaniya']);

        return $this->render('create-obrazovanie', ['model' => $model]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete-obrazovanie' => ['post'],
                ],
            ],
        ];
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