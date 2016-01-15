<?php
namespace app\controllers;

use app\components\Controller;
use app\entities\ObrazovanieFizLica;
use app\models\lichnye_dannye_obrazovanie\ObrazovanieForm;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Yii;

class LichnyeDannyeObrazovanieController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
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

    public function actionIndex()
    {
        $query = ObrazovanieFizLica::find()
            ->with('organizaciyaRel', 'kvalifikaciyaRel')
            ->hasFizLico();

        $data = new ActiveDataProvider([
            'query' => $query,
            'key' => 'hashids',
            'pagination' => false,
            'sort' => false
        ]);

        return $this->render('index', ['data' => $data]);
    }

    public function actionUpdate($id)
    {
        return $this->createUpdateHelper($this->findModel($id));
    }

    public function actionCreate()
    {
        $model = new ObrazovanieForm;
        $model->fiz_lico = Yii::$app->user->fizLicoId;
        return $this->createUpdateHelper($model);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->withDirectoriesSafeDelete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return ObrazovanieForm
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        $model = ObrazovanieForm::findOneByHashids($id);
        if (!$model)
            throw new NotFoundHttpException;

        if ($model->fiz_lico != Yii::$app->user->fizLicoId)
            throw new ForbiddenHttpException;

        return $model;
    }

    /**
     * @param $model ObrazovanieFizLica
     * @return string|\yii\web\Response
     */
    private function createUpdateHelper($model)
    {
        if ($model->load(Yii::$app->request->post()) && $model->withDirectoriesSafeSave())
            return $this->redirect(['index']);

        return $this->render('form', ['model' => $model]);
    }
}