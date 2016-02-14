<?php
namespace app\controllers;

use app\components\Controller;
use app\components\JsResponse;
use app\entities\FizLico;
use app\entities\Kurs;
use app\enums\Rol;
use app\enums\StatusZapisiNaKurs;
use app\enums\TipKursa;
use app\helpers\Hashids;
use app\models\kursy\SpisokKursovFilterForm;
use yii\console\Response;
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

    public function actionIzmenitStatusKursa(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = new JsResponse();
        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status');
        $error = Kurs::isVariativnijRazdelHasError($id);
        if (!$error) {
            $kurs = Kurs::findOne($id);
            $kurs->statusProgrammy = $status;
            if (!$kurs->save()) {
                $response->type = JsResponse::ERROR;
                $response->msg = JsResponse::MSG_OPERATION_ERROR;
            }
        }
        else{
            $response->type = JsResponse::ERROR;
            $response->msg = 'Количество часов в блоках тем/дисциплинах вариативной части должно быть равным количеству часов первого блока тем/дисциплины вариативной части';
        }
        return $response;
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