<?php
namespace app\controllers;

use app\entities\Dolzhnost;
use app\enums\Rol;
use app\enums2\TipDolzhnosti;
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
                        'roles' => [
                            Rol::SOTRUDNIK_UCHEBNOGO_OTDELA,
                            Rol::SOTRUDNIK_OTDELA_ATTESTACII,
                            Rol::ADMINISTRATOR
                        ]
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

    public function actionUchitel()
    {
        $post = \Yii::$app->request->post();
        if($post){
            $uchitelja = Dolzhnost::find()->select('id')->where(['obschij' => true, 'tip' => TipDolzhnosti::UCHITEL_PREPODAVATEL])->column();
            $response = $post['selection'];
            $error = false;
            $transaction = \Yii::$app->db->beginTransaction();
            foreach ($response as $id){
                $dolzhnost = Dolzhnost::findOne(['id'=>$id]);
                if(!$dolzhnost->tip == TipDolzhnosti::UCHITEL_PREPODAVATEL){
                    $dolzhnost->tip = TipDolzhnosti::UCHITEL_PREPODAVATEL;
                    if (!$dolzhnost->save())$error = true;
                }
            }
            foreach ($uchitelja as $id){
                if (!in_array($id, $response)){
                    $dolzhnost = Dolzhnost::findOne(['id'=>$id]);
                    $dolzhnost->tip = null;
                    if (!$dolzhnost->save())$error = true;
                }
            }
            if (!$error) {
                $transaction->commit();
                \Yii::$app->session->setFlash('success','Данные успешно сохранены!',false);
                $this->redirect('/site/index');
            }else{
                $transaction->rollback();
                \Yii::$app->session->setFlash('danger','Данные не сохранены!',false);
                $this->redirect('/site/index');
            }
        } else {
            $provider = new ActiveDataProvider([
                'query' => Dolzhnost::find()->where(['obschij' => true, 'tip' => null])->orWhere(['tip' => TipDolzhnosti::UCHITEL_PREPODAVATEL])->orderBy('nazvanie'),
                'pagination' => false,
                'sort' => false
            ]);
            return $this->render('uchitel', [
                'provider' => $provider,
            ]);
        }
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