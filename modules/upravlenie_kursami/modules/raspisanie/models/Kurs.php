<?php
namespace app\upravlenie_kursami\raspisanie\models;

use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use app\records\RazdelKursa;
use app\records\StrukturnoePodrazdelenie;

use app\upravlenie_kursami\models\FizLico;

class Kurs extends \app\upravlenie_kursami\models\Kurs
{
    public function getUnused_podrazdely()
    {
        $query = $this->getRazdely_kursa_rel()
            ->orderBy('nomer')
            ->with([
                'podrazdely_rel' => function (ActiveQuery $q) {$q->orderBy('nomer');},
                'podrazdely_rel.temy_with_unused_chasti_rel'
            ]);
        
        $podrazdely = [];
        
        foreach ($query->all() as $razdel) {
            /* @var RazdelKursa $razdel */
            foreach ($razdel->podrazdely_rel as $podrazdel) {
                /* @var PodrazdelKursa $podrazdel */
                if (count($podrazdel->temy_with_unused_chasti_rel))
                    $podrazdely[] = $podrazdel;
            }
        }
        
        return $podrazdely;
    }

    public function getTemy_with_unused_chasti()
    {
        $temy = [];

        foreach ($this->getUnused_podrazdely() as $podrazdel) {
            /* @var PodrazdelKursa $podrazdel */
            $temy = ArrayHelper::merge($temy, $podrazdel->temy_with_unused_chasti_rel);
        }

        return $temy;
    }

    public function getPrepodavateli_from_unused_temy()
    {
        $prepodavateliIds = ArrayHelper::getColumn($this->getTemy_with_unused_chasti(), 'prepodavatel_fiz_lico');
        $prepodavateliIds = array_filter(array_unique($prepodavateliIds));

        return FizLico::find()->where(['id' => $prepodavateliIds])->all();
    }

    public function getStrukturnye_podrazdeleniya_from_unused_temy()
    {
        $ids = ArrayHelper::getColumn(
            $this->getPrepodavateli_from_unused_temy(),
            'pervoe_strukturnoe_podrazdelenie_briop.id'
        );

        $ids = array_unique($ids);

        return StrukturnoePodrazdelenie::find()->where(['id' => $ids])->all();
    }

    public function getNedeli_from_unused_temy()
    {        
        return array_unique(ArrayHelper::getColumn($this->getTemy_with_unused_chasti(), 'nedelya'));
    }

    public function getZanyatiya_rel()
    {
        $q = (new Query)
            ->select([
                'zct.zanyatie',
                'r.kurs'
            ])
            ->from('zanyatie_chasti_temy zct')
            ->leftJoin('tema t', 't.id = zct.tema')
            ->leftJoin('podrazdel_kursa p', 'p.id = t.podrazdel')
            ->leftJoin('razdel_kursa r', 'r.id = p.razdel');

        return Zanyatie::find()
            ->leftJoin(['q' => $q], 'q.zanyatie = zanyatie.id')
            ->where(['q.kurs' => $this->id]);
    }
}