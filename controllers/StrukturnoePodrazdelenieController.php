<?php

namespace app\controllers;

use app\enums2\Rol;
use Yii;
use app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie;
use app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenieSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\organizaciya\Organizaciya;
use yii\db\Exception;

/**
 * StrukturnoePodrazdelenieController implements the CRUD actions for StrukturnoePodrazdelenie model.
 */
class StrukturnoePodrazdelenieController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        // all the action are accessible to SOTRUDNIK_UCHEBNOGO_OTDELA and ADMINISTRATOR
                        'allow' => true,
                        'roles' => [
                            Rol::ADMINISTRATOR,
                            Rol::SOTRUDNIK_UCHEBNOGO_OTDELA,
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all StrukturnoePodrazdelenie models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StrukturnoePodrazdelenieSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StrukturnoePodrazdelenie model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new StrukturnoePodrazdelenie model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StrukturnoePodrazdelenie();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model
            ]);
        }
    }

    /**
     * Updates an existing StrukturnoePodrazdelenie model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'orgname' => Organizaciya::find()->orderBy('id ASC')->all(),
            ]);
        }
    }

    /**
     * Deletes an existing StrukturnoePodrazdelenie model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        try {
            $model->delete();
        }
        catch(Exception $e){
            \Yii::$app->session->setFlash('danger','Данные не удалены! Подразделение уже используется в документах.',false);
            return $this->redirect(['index']);
        }
        \Yii::$app->session->setFlash('success','Подразделение удалено!',false);
        return $this->redirect(['index']);
    }

    /**
     * Finds the StrukturnoePodrazdelenie model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StrukturnoePodrazdelenie the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StrukturnoePodrazdelenie::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
}
