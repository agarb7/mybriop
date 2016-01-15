<?php
namespace app\controllers;

use app\components\Controller;
use app\entities\RabotaFizLica;
use app\models\lichnye_dannye_rabota\RabotaForm;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Yii;

class LichnyeDannyeRabotaController extends Controller
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
        $query = RabotaFizLica::find()
            ->with('organizaciyaRel')
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
        $model = new RabotaForm;
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
     * @return RabotaForm
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        $model = RabotaForm::findOneByHashids($id);
        if (!$model)
            throw new NotFoundHttpException;

        if ($model->fiz_lico != Yii::$app->user->fizLicoId)
            throw new ForbiddenHttpException;

        return $model;
    }

    /**
     * @param $model RabotaForm
     * @return string|\yii\web\Response
     */
    private function createUpdateHelper($model)
    {
        if ($model->load(Yii::$app->request->post()) && $model->withDirectoriesSafeSave())
            return $this->redirect(['index']);

        return $this->render('form', ['model' => $model]);
    }
}