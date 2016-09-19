<?php
namespace app\upravlenie_kursami\potok\controllers;

use app\enums2\FormaZanyatiya;
use app\enums2\StatusRaspisaniyaKursa;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;

use app\enums2\Rol;

use app\upravlenie_kursami\models\FizLico;
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
        $prepodavateli = FizLico::findPrepodavateli()
            ->select('fiz_lico.id, familiya, imya, otchestvo')
            ->listItems(function ($fizLico) {
                return Yii::$app->formatter->asFizLico($fizLico);
            });

        return $this->render('index', [
            'prepodavateli' => $prepodavateli
        ]);
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
            ->customInfo()
            ->applyFilter($filter)
            ->orderBy('least([[ochnoe_nachalo]], [[zaochnoe_nachalo]])')
            ->formatted();
    }

    //todo filters
    public function actionTemaList($kurs)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return Tema::find()
            ->customInfo()
            ->where(['kurs.id' => $kurs])
            ->formatted();
    }

    public function actionCreateZanyatie()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->createZanyatie(Yii::$app->request->post()))
            throw new BadRequestHttpException;

        return 'ok';
    }

    public function actionDeleteZanyatie($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->deleteZanyatie($id))
            throw new BadRequestHttpException;

        return 'ok';
    }

    public function actionTemyZanyatiyaList($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return Tema::find()
            ->customInfo()
            ->whereZanyatie($id)
            ->formatted();
    }

    public function actionAllowRaspisanie($kurs, $allow)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->allowRaspisanie($kurs, $allow))
            throw new BadRequestHttpException;

        return 'ok';
    }

    private function createZanyatie($post)
    {
        $zanyatie = new Zanyatie;

        if (!$zanyatie->load($post, ''))
            return false;

        $zanyatie->forma = FormaZanyatiya::OCHNAYA;

        return $zanyatie->save();
    }

    private function deleteZanyatie($id)
    {
        $zanyatie = Zanyatie::findOne($id);

        return $zanyatie && $zanyatie->delete();
    }

    private function allowRaspisanie($kurs, $allow)
    {
        $kursRecord = Kurs::findOne($kurs);
        if (!$kursRecord)
            return false;

        $kursRecord->status_raspisaniya = $allow
            ? StatusRaspisaniyaKursa::REDAKTIRUETSYA
            : null;

        return $kursRecord->save();
    }
}