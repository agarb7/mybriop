<?php
namespace app\controllers;

use app\components\Controller;
use app\entities\Kim;
use app\entities\KursExtended;
use app\enums\Rol;
use app\enums\TipKursa;
use app\enums2\StatusProgrammyKursa;
use app\helpers\Hashids;
use app\models\kurs_slushatelyu\InfoOIupForm;
use app\models\kurs_slushatelyu\SpisokKursovFilterForm;
use app\models\kurs_slushatelyu\ZapisNaKursForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class KursSlushatelyuController extends Controller
{
    public function actionProgrammaKursa($kurs)
    {
        $kurs = Hashids::decodeOne($kurs);
        if (!$kurs)
            throw new NotFoundHttpException;

        /* @var $kursRecord KursExtended */
        $kursRecord = KursExtended::findOne($kurs);
        if (!$kursRecord)
            throw new NotFoundHttpException;

        if (!Yii::$app->user->can(Rol::SOTRUDNIK_UCHEBNOGO_OTDELA)) {
            if ($kursRecord->getAvailableAction()[0] !== KursExtended::AVAILABLE_ACTION_PROGRAMMA)
                throw new HttpException(422);

            if ($kursRecord->statusProgrammy !== StatusProgrammyKursa::ZAVERSHENA)
                return $this->render('programma-kursa-isnt-ready', compact('kursRecord'));
        }

        return $this->render('programma-kursa', compact('kursRecord'));
    }

    public function actionKimTekst($kim)
    {
        $kim = Hashids::decodeOne($kim);
        if (!$kim)
            throw new NotFoundHttpException;

        $kimRecord = Kim::findOne($kim);
        if (!$kimRecord)
            throw new NotFoundHttpException;

        //todo check access ability

        return $this->render('kim_tekst', compact('kimRecord'));
    }

    public function actionZapisNaKursPk()
    {
        return $this->renderZapisNaKurs(TipKursa::PK);
    }

    public function actionZapisNaKursPp()
    {
        return $this->renderZapisNaKurs(TipKursa::PP);
    }

    public function actionZapisNaKursPo()
    {
        return $this->renderZapisNaKurs(TipKursa::PO);
    }

    public function actionMoiKursy($god = null)
    {
        $data = new ActiveDataProvider([
            'query' => KursExtended::findMyAsSlushatel()
                ->andFilterWhere(['plan_prospekt_god' => $god])
                ->orderBy('id'),
            'sort' => false
        ]);

        return $this->render('moi-kursy', ['data' => $data]);
    }

    public function actionZapisNaByudzhet($kurs)
    {
        if (!$this->actionIsAvailable($kurs, KursExtended::AVAILABLE_ACTION_BYUDZHET))
            throw new HttpException(422);

        $post = Yii::$app->request->post();
        $model = $this->createZapisNaKursModel($kurs, ZapisNaKursForm::SCENARIO_ZAPIS_BYUDZHET);

        if ($model->load($post) && $model->zapisatsyaByudzhet())
            return $this->render('zapisany-na-kurs', compact('model'));

        $model->populateByudzhet();
        return $this->render('zapis-na-kurs', compact('model', 'kurs'));
    }

    public function actionZapisNaVnebyudzhet($kurs)
    {
        if (!$this->actionIsAvailable($kurs, KursExtended::AVAILABLE_ACTION_VNEBYUDZHET))
            throw new HttpException(422);

        $post = Yii::$app->request->post();
        $model = $this->createZapisNaKursModel($kurs, ZapisNaKursForm::SCENARIO_ZAPIS_VNEBYUDZHET);

        if ($model->load($post) && $model->zapisatsyaVnebyudzhet())
            return $this->render('zapisany-na-kurs', compact('model'));

        $model->populateVnebyudzhet();
        return $this->render('zapis-na-kurs', compact('model', 'kurs'));
    }

    public function actionOtmenitZapis($kurs)
    {
        if (!$this->actionIsAvailable($kurs, KursExtended::AVAILABLE_ACTION_OTMENIT))
            throw new HttpException(422);

        $model = $this->createZapisNaKursModel($kurs, ZapisNaKursForm::SCENARIO_OTMENA_ZAPISI);

        if ($model->otmenitZapis())
            return $this->render('zapis-otmenena', compact('model'));

        return $this->goBack();
    }

    public function actionInfoOPodacheZayavki($kurs)
    {
        if (!$this->actionIsAvailable($kurs, KursExtended::AVAILABLE_ACTION_INFO_O_PODACHE))
            throw new HttpException(422);

        return $this->render('info-o-podache-zayavki');
    }

    public function actionInfoOIup($kurs)
    {
        if (!$this->actionIsAvailable($kurs, KursExtended::AVAILABLE_ACTION_IUP))
            throw new HttpException(422);

        $model = new InfoOIupForm;

        if (Yii::$app->request->isPost && $model->iup($kurs))
            $this->redirect(['zapis-na-kurs-pp']);

        return $this->render('info-o-iup', compact('model'));
    }


    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            '*' => Rol::PEDAGOGICHESKIJ_RABOTNIK,
            'programma-kursa' => Rol::SOTRUDNIK_UCHEBNOGO_OTDELA
        ];
    }

    private function renderZapisNaKurs($tip)
    {
        $filterModel = new SpisokKursovFilterForm;
        $provider = $filterModel->search($tip, Yii::$app->request->get());

        return $this->render('spisok-kursov', compact('tip', 'provider', 'filterModel'));
    }

    /**
     * @param $kurs
     * @param $scenario
     * @return ZapisNaKursForm
     */
    private function createZapisNaKursModel($kurs, $scenario)
    {
        if ($fiz_lico = Yii::$app->user->fizLico)
            $fizLico = $fiz_lico->id;

        $model = new ZapisNaKursForm(compact('kurs','fizLico'));
        $model->setScenario($scenario);
        return $model;
    }

    private function actionIsAvailable($kurs, $action)
    {
        /* @var $kursRecord KursExtended */
        $kursRecord = KursExtended::find()->where(['kurs.id' => $kurs])->one();
        if (!$kursRecord || $kursRecord->getAvailableAction()[0] !== $action)
            return false;

        return true;
    }
}
