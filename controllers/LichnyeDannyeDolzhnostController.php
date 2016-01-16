<?php
namespace app\controllers;

use app\components\Controller;
use app\entities\DolzhnostFizLicaNaRabote;
use app\entities\RabotaFizLica;
use app\models\lichnye_dannye_dolzhnost\DolzhnostForm;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Yii;

class LichnyeDannyeDolzhnostController extends Controller
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

    public function actionIndex($rabota)
    {
        $decodedRabota = Yii::$app->hashids->decode($rabota);
        if (!$decodedRabota)
            throw new NotFoundHttpException;
        $rabotaId = $decodedRabota[0];

        $query = DolzhnostFizLicaNaRabote::find()
            ->joinWith('rabotaFizLicaRel')
            ->with('dolzhnostRel')
            ->where([
                'rabota_fiz_lica.id' => $rabotaId,
                'rabota_fiz_lica.fiz_lico' => Yii::$app->user->fizLicoId
            ]);

        $data = new ActiveDataProvider([
            'query' => $query,
            'key' => 'hashids',
            'pagination' => false,
            'sort' => false
        ]);

        return $this->render('index', ['data' => $data, 'rabota' => $rabota]);
    }

    public function actionUpdate($id)
    {
        return $this->createUpdateHelper($this->findModel($id));
    }

    public function actionCreate($rabota)
    {
        $rabotaModel = RabotaFizLica::findOneByHashids($rabota);
        if (!$rabotaModel)
            throw new NotFoundHttpException;

        if ($rabotaModel->fiz_lico !== Yii::$app->user->fizLicoId)
            throw new ForbiddenHttpException;

        $model = new DolzhnostForm;
        $model->rabota_fiz_lica = $rabotaModel->id;

        return $this->createUpdateHelper($model);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $rabota = $this->rabotaHashids($model);
        $model->withDirectoriesSafeDelete();

        return $this->redirect(['index', 'rabota' => $rabota]);
    }

    /**
     * @param $id
     * @return DolzhnostForm
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        $model = DolzhnostForm::findOneByHashids($id);
        if (!$model)
            throw new NotFoundHttpException;

        if ($model->rabotaFizLicaRel->fiz_lico !== Yii::$app->user->fizLicoId)
            throw new ForbiddenHttpException;

        return $model;
    }

    /**
     * @param $model DolzhnostForm
     * @return string|\yii\web\Response
     */
    private function createUpdateHelper($model)
    {
        if ($model->load(Yii::$app->request->post()) && $model->withDirectoriesSafeSave())
            return $this->redirect(['index', 'rabota' => $this->rabotaHashids($model)]);

        return $this->render('form', ['model' => $model]);
    }

    /**
     * @param $model DolzhnostFizLicaNaRabote
     * @return string
     */
    private function rabotaHashids($model)
    {
        return Yii::$app->hashids->encode($model->rabota_fiz_lica);
    }
}