<?php

namespace app\controllers;

use Yii;
use app\models\organizaciya\Organizaciya;
use app\models\organizaciya\OrganizaciyaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Exception;
use yii\filters\AccessControl;
use app\enums2\Rol;

/**
 * OrganizaciyaController implements the CRUD actions for Organizaciya model.
 */
class OrganizaciyaController extends Controller
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
     * Lists all Organizaciya models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrganizaciyaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Organizaciya model.
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
     * Creates a new Organizaciya model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Organizaciya();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Organizaciya model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->etapy_obrazovaniya = explode(',', trim($model->etapy_obrazovaniya, '{}'));

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Organizaciya model.
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
            \Yii::$app->session->setFlash('danger','Данные не удалены! Организация уже используется в документах.',false);
            return $this->redirect(['index']);
        }
        \Yii::$app->session->setFlash('success','Организация удалена!',false);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Organizaciya model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Organizaciya the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Organizaciya::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
