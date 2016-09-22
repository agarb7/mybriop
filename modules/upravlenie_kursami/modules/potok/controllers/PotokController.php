<?php
namespace app\upravlenie_kursami\potok\controllers;

use app\upravlenie_kursami\models\FizLico;
use app\upravlenie_kursami\potok\models\potok\Tema;
use app\upravlenie_kursami\potok\models\potok\Kurs;
use app\upravlenie_kursami\potok\models\potok\KursFilter;
use app\upravlenie_kursami\potok\models\potok\TemaFilter;
use app\upravlenie_kursami\potok\models\potok\Zanyatie;

use app\enums2\FormaZanyatiya;
use app\enums2\StatusRaspisaniyaKursa;
use app\enums2\Rol;

use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;

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
            ->orderBy('familiya, imya, otchestvo')
            ->listItems(function ($fizLico) {
                return Yii::$app->formatter->asFizLico($fizLico);
            });

        $years = Kurs::find()
            ->select([
                'god' => 'extract(year from [[plan_prospekt_god]])'
            ])
            ->groupBy('plan_prospekt_god')
            ->orderBy('god')
            ->listItems('god', 'god');

        return $this->render('index', compact('prepodavateli', 'years'));
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
            ->filter($filter)
            ->orderBy('least([[ochnoe_nachalo]], [[zaochnoe_nachalo]])')
            ->formatted();
    }

    //todo filters
    public function actionTemaList($kurs)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Tema::find()
            ->customInfo()
            ->where(['kurs.id' => $kurs]);

        $filter = new TemaFilter;

        if ($filter->load(Yii::$app->request->get(), '') && !$query->andFilter($filter))
            throw new BadRequestHttpException;

        return $query->formatted();
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
        //todo canCreateZanyatie

        $zanyatie = new Zanyatie;

        if (!$zanyatie->load($post, ''))
            return false;

        $zanyatie->forma = FormaZanyatiya::OCHNAYA;

        return $zanyatie->save();
    }

    private function deleteZanyatie($id)
    {
        //todo canDeleteZanyatie

        $zanyatie = Zanyatie::findOne($id);

        return $zanyatie && $zanyatie->delete();
    }

    private function allowRaspisanie($kurs, $allow)
    {
        //todo canAllow

        $kursRecord = Kurs::findOne($kurs);
        if (!$kursRecord)
            return false;

        $kursRecord->status_raspisaniya = $allow
            ? StatusRaspisaniyaKursa::REDAKTIRUETSYA
            : null;

        return $kursRecord->save();
    }
}