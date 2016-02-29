<?php
namespace app\modules\plan_prospekt\controllers;

use app\modules\plan_prospekt\models\KursIup;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\AccessControl;
use Yii;

use app\enums2\Rol;
use app\records\FizLico;
use app\records\KategoriyaSlushatelya;
use app\modules\plan_prospekt\models\KursDelete;
use app\modules\plan_prospekt\models\KursForm;
use app\modules\plan_prospekt\models\KursSearch;

class EditorController extends Controller
{
    private $_kursSearchFormName;
    private $_kategoriiSlushatelej;
    private $_rukovoditeliKursov;

    public function init()
    {
        $this->_kursSearchFormName = (new KursSearch)->formName();
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
        return $this->render('index', ['gridParams' => $this->createGridParams()]);
    }

    public function actionUpdate($id)
    {
        /* @var $model KursForm */
        $model = KursForm::findOne($id);

        if ($model && $model->load(Yii::$app->request->post()) && $model->save())
            $model = null;

        return $this->renderAction('_edit', [
            'model' => $model,
            'rukovoditeliKursov' => $this->rukovoditeliKursov(),
            'kategoriiSlushatelej' => $this->kategoriiSlushatelej(),
            'modalSize' => 'modal-lg',
            'modalTitle' => 'Редактирование записи'
        ]);
    }

    public function actionCreate()
    {
        $model = new KursForm;

        if ($model->load(Yii::$app->request->post())) {
            $model->plan_prospekt_god = Yii::$app->request->get('year') . '-01-01';

            if ($model->save())
                $model = null;
        }

        return $this->renderAction('_edit', [
            'model' => $model,
            'rukovoditeliKursov' => $this->rukovoditeliKursov(),
            'kategoriiSlushatelej' => $this->kategoriiSlushatelej(),
            'modalSize' => 'modal-lg',
            'modalTitle' => 'Создание записи'
        ]);
    }

    public function actionDelete($id)
    {
        /* @var $model KursDelete */
        $model = KursDelete::findOne($id);

        if ($model && Yii::$app->request->isPost && $model->canBeDeleted && $model->delete())
            $model = null;

        return $this->renderAction('_delete', [
            'model' => $model,
            'modalTitle' => 'Удаление записи'
        ]);
    }

    public function actionIup($id)
    {
        /* @var $model KursIup */
        $model = KursIup::findOne($id);

        if ($model && $model->load(Yii::$app->request->post()) && $model->save())
            $model = null;

        return $this->renderAction('_iup', [
            'model' => $model,
            'modalTitle' => 'Индивидуальный учебный план'
        ]);
    }

    private function createUrl($route, $preserveParams)
    {
        $preservedParams = array_intersect_key(
            Yii::$app->request->get(),
            array_flip($preserveParams)
        );

        $route = ArrayHelper::merge($preservedParams,$route);

        return Url::to($route);
    }

    private function createActionUrl($action, $id = null)
    {
        return $this->createUrl(
            [0 => $action, 'id' => $id],
            ['year', 'page', 'sort', $this->_kursSearchFormName]
        );
    }

    private function createFormActionUrl()
    {
        return $this->createUrl(
            [0 => 'index'],
            ['year', 'sort']
        );
    }

    private function createGridParams()
    {
        $searchModel = new KursSearch;

        return [
            'dataProvider' => $searchModel->search(Yii::$app->request->get()),

            'searchModel' => $searchModel,
            'rukovoditeliKursov' => $this->rukovoditeliKursov(),
            'kategoriiSlushatelej' => $this->kategoriiSlushatelej(),

            'formActionUrl' => $this->createFormActionUrl(),
            'actionColumnUrlCreator' => function ($action, $model, $key) {
                return $this->createActionUrl($action, $key);
            },
        ];
    }

    private function kategoriiSlushatelej()
    {
        if ($this->_kategoriiSlushatelej === null) {
            $data = KategoriyaSlushatelya::find()
                ->orderBy('nazvanie')
                ->asArray()
                ->all();

            $this->_kategoriiSlushatelej = ArrayHelper::map($data, 'id', 'nazvanie');
        }

        return $this->_kategoriiSlushatelej;
    }

    private function rukovoditeliKursov()
    {
        if ($this->_rukovoditeliKursov === null) {
            $data = FizLico::find()
                ->joinWith('polzovateli_rel')
                ->orderBy('familiya, imya, otchestvo')
                ->groupBy('fiz_lico.id')
                ->where(Yii::$app->db->quoteValue(Rol::RUKOVODITEL_KURSOV) . ' = any ([[roli]])')
                ->asArray()
                ->all();

            $this->_rukovoditeliKursov = ArrayHelper::map($data, 'id', function ($fizLico) {
                return Yii::$app->formatter->asFizLico($fizLico);
            });
        }

        return $this->_rukovoditeliKursov;
    }

    private function renderAction($subview, $params)
    {
        if (!isset($params['indexUrl']))
            $params['indexUrl'] = $this->createActionUrl('index');

        if (!Yii::$app->request->isPjax)
            $viewParams['gridParams'] = $this->createGridParams();

        $viewParams['actionSubview'] = $subview;
        $viewParams['actionParams'] = $params;

        return $this->render('index', $viewParams);
    }
}