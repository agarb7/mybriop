<?php
namespace app\modules\plan_prospekt\controllers;

use app\modules\plan_prospekt\models\KursDelete;
use app\modules\plan_prospekt\models\KursForm;
use app\modules\plan_prospekt\models\KursSearch;
use app\enums\Rol;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\filters\AccessControl;
use Yii;

class EditorController extends Controller
{
    private $_preservedParamsHash;

    public function init()
    {
        $this->_preservedParamsHash = array_flip(['year', 'page', 'sort', (new KursSearch)->formName()]);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Rol::SOTRUDNIK_UCHEBNOGO_OTDELA]
                    ]
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->pjaxAwareRender('index', $this->indexParams());
    }

    public function actionUpdate($id)
    {
        $backUrl = $this->indexUrl();

        /* @var $model KursForm */
        $model = KursForm::findOne($id);

        if ($model && $model->load(Yii::$app->request->post()) && $model->save())
            $model = null;

        return $this->pjaxAwareRender('update', compact('model', 'backUrl'));
    }

    public function actionCreate()
    {
        $backUrl = $this->indexUrl();

        $model = new KursForm;

        if ($model->load(Yii::$app->request->post())) {
            $model->plan_prospekt_god = Yii::$app->request->get('year') . '-01-01';

            if ($model->save())
                $model = null;
        }

        return $this->pjaxAwareRender('create', compact('model', 'backUrl'));
    }

    public function actionDelete($id)
    {
        $backUrl = $this->indexUrl();

        /* @var $model KursDelete */
        $model = KursDelete::findOne($id);

        if ($model && Yii::$app->request->isPost && $model->canBeDeleted && $model->delete())
            $model = null;

        return $this->pjaxAwareRender('delete', compact('model', 'backUrl'));
    }

    public function actionIup($id)
    {
        $backUrl = $this->indexUrl();

        /* @var $model KursForm */
        $model = KursForm::findOne($id);

        if ($model && $model->load(Yii::$app->request->post()) && $model->save())
            $model = null;

        return $this->pjaxAwareRender('iup', compact('model', 'backUrl'));
    }

    private function createUrl($action, $model = null, $key = null)
    {
        $route = array_intersect_key(Yii::$app->request->get(), $this->_preservedParamsHash);
        $route[0] = $action;
        $route['id'] = $key;
        return Url::to($route);
    }

    private function indexUrl()
    {
        return $this->createUrl('index');
    }

    private function indexParams()
    {
        return [
            'dataProvider' => (new KursSearch)->search(Yii::$app->request->get()),
            'urlCreator' => function ($action, $model, $key) {
                return $this->createUrl($action, $model, $key);
            },
        ];
    }

    private function renderLayout($params = [])
    {
        if (!isset($params['indexParams']))
            $params['indexParams'] = $this->indexParams();

        return $this->render('layout', $params);
    }

    private function pjaxAwareRender($view, $params)
    {
        return Yii::$app->request->isPjax
            ? $this->view->render($view, $params, $this)
            : $this->renderLayout([$view . 'Params' => $params]);
    }
}