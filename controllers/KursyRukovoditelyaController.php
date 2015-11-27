<?php
namespace app\controllers;

use app\components\Controller;
use app\entities\FizLico;
use app\entities\Kurs;
use app\entities\KursExtended;
use app\enums\Rol;
use app\enums\StatusZapisiNaKurs;
use app\helpers\Hashids;
use app\models\kursy_rukovoditelya\ZapisModel;
use yii\data\ActiveDataProvider;
use Yii;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class KursyRukovoditelyaController extends Controller
{
    public function actionSpisok()
    {
        $data = new ActiveDataProvider([
            'query' => KursExtended::findMyAsRukovoditel()->orderBy('id'),
            'key' => 'hashids',
            'sort' => false
        ]);

        return $this->render('spisok', compact('data'));
    }

    public function actionSlushateli($kurs)
    {
        $kurs = Hashids::decodeOne($kurs);
        if (!$kurs)
            throw new NotFoundHttpException;

        $kursRecord = Kurs::findOne($kurs);
        if (!$kursRecord)
            throw new NotFoundHttpException;

        $model = new ZapisModel;
        if ($model->load(Yii::$app->request->post())) {
            $model->kursId = $kurs;

            //todo only rukovoditel can

            if (!$model->applyStatus())
                throw new HttpException(422);
        }

        //todo with()
        $query = FizLico::findSlushateliKursa($kurs)
            ->andWhere([
                'kurs_fiz_lica.status' => [
                    StatusZapisiNaKurs::asSql(StatusZapisiNaKurs::ZAPIS),
                    StatusZapisiNaKurs::asSql(StatusZapisiNaKurs::OTMENENO_RUKOVODITELEM)
                ]
            ])
            ->orderBy('kurs_fiz_lica.status, id');

        $data = new ActiveDataProvider([
            'query' => $query,
            'key' => 'hashids',
            'pagination' => false,
            'sort' => false
        ]);

        return $this->render('slushateli', compact('data', 'kursRecord', 'model'));
    }

    /**
     * @inheritdoc
     */
    public function accessRules()
    {
        return [
            '*' => Rol::RUKOVODITEL_KURSOV
        ];
    }
}