<?php
namespace app\controllers;

use app\components\Controller;
use app\entities\FizLico;
use app\entities\Kurs;
use app\enums\Rol;
use app\enums\StatusZapisiNaKurs;
use app\enums\TipKursa;
use app\helpers\Hashids;
use app\models\kursy\SpisokKursovFilterForm;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\NotFoundHttpException;


class KursyController extends Controller
{
    public function actionSpisokPp()
    {
        return $this->spisok(TipKursa::PP);
    }

    public function actionSpisokPk()
    {
        return $this->spisok(TipKursa::PK);
    }

    public function actionSpisokPo()
    {
        return $this->spisok(TipKursa::PO);
    }

    public function actionSlushateli($kurs)
    {
        $kurs = Hashids::decodeOne($kurs);
        if (!$kurs)
            throw new NotFoundHttpException;

        $model = Kurs::findOne($kurs);
        if (!$model)
            throw new NotFoundHttpException;

        //todo with()
        $query = FizLico::findSlushateliKursa($kurs)
            ->andWhere(['kurs_fiz_lica.status' => StatusZapisiNaKurs::asSql(StatusZapisiNaKurs::ZAPIS)])
            ->orderBy('id');

        $data = new ActiveDataProvider([
            'query' => $query,
            'key' => 'hashids',
            'pagination' => false,
            'sort' => false
        ]);

        return $this->render('slushateli', compact('data', 'model'));
    }

    private function spisok($tip)
    {
        $filterModel = new SpisokKursovFilterForm;
        $data = $filterModel->search($tip, Yii::$app->request->get());

        return $this->render('spisok', compact('tip', 'data', 'filterModel'));
    }

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            '*' => Rol::SOTRUDNIK_UCHEBNOGO_OTDELA
        ];
    }
}