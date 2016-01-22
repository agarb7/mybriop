<?php
namespace app\controllers;

use app\entities\Dolzhnost;
use app\enums\Rol;
use app\models\dolzhnost\DolzhnostModel;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;

class DolzhnostController extends Controller
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
                    'merge' => ['post'],
                    'move' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Rol::SOTRUDNIK_UCHEBNOGO_OTDELA, Rol::SOTRUDNIK_OTDELA_ATTESTACII]
                    ]
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'commonData' => $this->createDataProvider(true),
            'privateData' => $this->createDataProvider(false),
        ]);
    }

    public function actionMerge()
    {
        return $this->mergeHelper('Объеденить / переименовать');
    }

    public function actionMove()
    {
        return $this->mergeHelper('Объеденить / сделать общим / переименовать');
    }

    private function createDataProvider($commonFlag)
    {
        return new ActiveDataProvider([
            'query' => Dolzhnost::find()->where(['obschij' => $commonFlag])->orderBy('nazvanie'),
            'key' => 'hashids',
            'pagination' => false,
            'sort' => false
        ]);
    }

    private function mergeHelper($actionCaption)
    {
        $model = new DolzhnostModel;
        $model->scenario = DolzhnostModel::SCENARIO_MERGE;

        if ($model->load(Yii::$app->request->post()) && $model->merge())
            return $this->redirect(['index']);

        if (!$model->loadDolzhnosti())
            return $this->redirect(['index']);

        $model->guessName();

        return $this->render('form', [
            'model' => $model,
            'actionCaption' => $actionCaption
        ]);
    }
}