<?php
namespace app\controllers;

use app\components\Controller;
use app\entities\Kim;
use app\entities\KursExtended;
use app\enums\Rol;
use app\enums\StatusZapisiNaKurs;
use app\enums\TipKursa;
use app\helpers\Hashids;
use app\models\kurs_slushatelyu\SpisokKursovFilterForm;
use app\models\kurs_slushatelyu\ZapisNaKursForm;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class KursSlushatelyuController extends Controller
{
    public function actionProgrammaKursa($kurs)
    {
        $kurs = Hashids::decodeOne($kurs);
        if (!$kurs)
            throw new NotFoundHttpException;

        $kursRecord = KursExtended::findOne($kurs);
        if (!$kursRecord)
            throw new NotFoundHttpException;

        //todo refactor as rule
        if (!$kursRecord->isUserZapisan || !$kursRecord->isInDuration())
            throw new HttpException(422);

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

    public function actionMoiKursy()
    {
        return $this->render('moi-kursy');
    }

    public function actionZapisNaByudzhet($kurs)
    {
        if (!$this->userCanChangeZapis($kurs, StatusZapisiNaKurs::ZAPIS))
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
        if (!$this->userCanChangeZapis($kurs, StatusZapisiNaKurs::ZAPIS))
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
        if (!$this->userCanChangeZapis($kurs, StatusZapisiNaKurs::OTMENA_ZAPISI))
            throw new HttpException(422);

        $model = $this->createZapisNaKursModel($kurs, ZapisNaKursForm::SCENARIO_OTMENA_ZAPISI);

        if ($model->otmenitZapis())
            return $this->render('zapis-otmenena', compact('model'));

        return $this->goBack();
    }

    public function actionInfoOPodacheZayavki($kurs)
    {
        return $this->render('info-o-podache-zayavki');
    }

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            '*' => Rol::PEDAGOGICHESKIJ_RABOTNIK
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

    private function userCanChangeZapis($kurs, $new_status)
    {
        /**
         * @var $kursRecord KursExtended
         */
        $kursRecord = KursExtended::find()->where(['kurs.id' => $kurs])->one();
        if (!$kursRecord || $kursRecord->userCanNotChangeZapisReason($new_status))
            return false;

        return true;
    }
}
