<?php
namespace app\upravlenie_kursami\potok\controllers;

use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;

use app\enums2\Rol;

use app\upravlenie_kursami\potok\models\potok\Tema;
use app\upravlenie_kursami\potok\models\potok\Kurs;
use app\upravlenie_kursami\potok\models\potok\KursFilter;
use app\upravlenie_kursami\potok\models\potok\Zanyatie;

use Yii;

class PotokController extends Controller
{
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
        return $this->render('index');
    }

    public function actionKursList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $filter = new KursFilter;

        $filterIsGood =
            !$filter->load(Yii::$app->request->get(), '')
            || $filter->validate();

        if (!$filterIsGood)
            throw new BadRequestHttpException;

        return Kurs::find()
            ->setup()
            ->applyFilter($filter)
            ->orderBy('least([[ochnoe_nachalo]], [[zaochnoe_nachalo]])')
            ->formatted();
    }

    //todo filters
    public function actionTemaList($kurs)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return Tema::find()
            ->setup()
            ->where(['kurs.id' => $kurs])
            ->formatted();
    }

    public function actionCreateZanyatie()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $zanyatie = new Zanyatie;

        $zanyatieIsGood =
            $zanyatie->load(Yii::$app->request->get(), '')
            && $zanyatie->save();

        if (!$zanyatieIsGood)
            throw new BadRequestHttpException;
    }
}