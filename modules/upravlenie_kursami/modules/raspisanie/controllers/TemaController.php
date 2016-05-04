<?php
namespace app\upravlenie_kursami\raspisanie\controllers;

use Yii;

use yii\db\ActiveQuery;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

use app\enums2\Rol;
use app\records\Kurs;
use app\upravlenie_kursami\raspisanie\widgets\TemaPickerContent;
use app\upravlenie_kursami\raspisanie\models\PodrazdelKursa;

class TemaController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [Rol::RUKOVODITEL_KURSOV]
                    ]
                ],
            ]
        ];
    }

    public function actionIndex($kurs)
    {
        $kursRecord = Kurs::findOne($kurs);
        if (!$kursRecord)
            throw new NotFoundHttpException;

        $temySettings = function (ActiveQuery $q) {
            $q->orderBy('tema.nomer');
        };
        
        $podrazdelySettings = function (ActiveQuery $q) {
            $q->modelClass = PodrazdelKursa::className();
            $q->orderBy('nomer');
        }; 

        $query = $kursRecord
            ->getRazdely_kursa_rel()
            ->orderBy('nomer')
            ->with([
                'nazvanie_rel',
                'podrazdely_rel' => $podrazdelySettings,
                'podrazdely_rel.temy_with_unused_chasti_rel' => $temySettings,
                'podrazdely_rel.temy_with_unused_chasti_rel.prepodavatel_fiz_lico_rel',
            ]);
        
        return TemaPickerContent::widget([
            'data' => $query->all(),
        ]);
    }
}