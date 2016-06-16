<?php
namespace app\upravlenie_kursami\raspisanie\controllers;

use app\records\Tema;
use Yii;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;

use app\enums2\Rol;
use app\components\Formatter;

use app\records\StrukturnoePodrazdelenie;

use app\upravlenie_kursami\raspisanie\widgets\TemaPickerContent;
use app\upravlenie_kursami\raspisanie\models\TemaFilter;
use app\upravlenie_kursami\raspisanie\models\Kurs;

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
        
        $filter = new TemaFilter;
        $filter->load(Yii::$app->request->get());
        
        if (!$filter->validate())
            $filter = new TemaFilter;
        
        $temySettings = function (ActiveQuery $q) {
            $q->orderBy('tema.nomer');
        };
        
        $podrazdelySettings = function (ActiveQuery $q) {            
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
            'filter' => $filter
        ]);
    }

    public function actionFilterOptions($kurs, $attribute)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $kursRecord = Kurs::findOne($kurs);
        if (!$kursRecord)
            throw new NotFoundHttpException;
        
        switch ($attribute) {
            case 'podrazdel': return $this->getUnusedPodrazdely($kursRecord);
            case 'prepodavatel_fiz_lico': return $this->getPrepodavateliFromUnusedTemy($kursRecord);
            case 'prepodavatel_strukturnoe_podrazdelenie': return $this->getStrukturnyePodrazdeleniyaFromUnusedTemy($kursRecord);
            case 'nedelya': return $this->getNedeliFromUnusedTemy($kursRecord);
        }

        return [];
    }

    /**
     * @param Kurs $kurs
     * @return array
     */
    private function getUnusedPodrazdely($kurs)
    {
        $res = [['', '']];

        foreach ($kurs->getUnused_podrazdely() as $podrazdel)
            $res[] = [$podrazdel->id, $podrazdel->nazvanie];

        return $res;
    }

    /**
     * @param Kurs $kurs
     * @return array
     */
    private function getPrepodavateliFromUnusedTemy($kurs)
    {        
        $res = [['', '']];
        
        /* @var Formatter $formatter */
        $formatter = Yii::$app->formatter;

        foreach ($kurs->getPrepodavateli_from_unused_temy() as $prepodavatel)
            $res[] = [$prepodavatel->id, $formatter->asFizLico($prepodavatel)];

        return $res;
    }

    /**
     * @param Kurs $kurs
     * @return array
     */
    private function getStrukturnyePodrazdeleniyaFromUnusedTemy($kurs)
    {
        $res = [['', '']];

        foreach ($kurs->getStrukturnye_podrazdeleniya_from_unused_temy() as $strukturnoePodrazdelenie) {
            /* @var StrukturnoePodrazdelenie $strukturnoePodrazdelenie */
            $res[] = [
                $strukturnoePodrazdelenie->id,
                $strukturnoePodrazdelenie->nazvanie
            ];
        }

        return $res;
    }

    /**
     * @param Kurs $kurs
     * @return array
     */
    private function getNedeliFromUnusedTemy($kurs)
    {
        $res = [['', '']];

        foreach ($kurs->getNedeli_from_unused_temy() as $nedelya) {
            $res[] = [
                $nedelya,
                $nedelya . ' нед.'
            ];
        }

        return $res;
    }
}