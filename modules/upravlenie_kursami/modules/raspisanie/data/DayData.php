<?php
namespace app\upravlenie_kursami\raspisanie\data;

use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use app\upravlenie_kursami\models\Kurs;
use app\upravlenie_kursami\raspisanie\models\Zanyatie;
use app\upravlenie_kursami\raspisanie\models\Day;

class DayData extends BaseDataProvider
{
    /**
     * @var Kurs
     */
    public $kurs;

    /**
     * @var ActiveQuery
     */
    private $_query;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->kurs instanceof Kurs)
            throw new InvalidConfigException;
    }

    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        $days = [];

        /* @var $zanyatie Zanyatie */
        foreach ($this->getQuery()->each() as $zanyatie) {
            $date = $zanyatie->data;
            if (!isset($days[$date]))
                $days[$date] = new Day(['data' => $date]);

            /* @var $day Day */
            $day = $days[$date];
            $day->addZanyatie($zanyatie);
        }

        ksort($days);
        return array_values($days);
    }

    /**
     * @inheritdoc
     */
    protected function prepareKeys($models)
    {
        return ArrayHelper::getColumn($models, 'data');
    }

    /**
     * @inheritdoc
     */
    protected function prepareTotalCount()
    {
        $query = clone $this->getQuery();
        
        return $query->groupBy('data')->count();
    }

    /**
     * @return string|null
     */
    public function getFirstDate()
    {
        return ArrayHelper::getValue($this->kurs, 'raspisanie_nachalo');
    }

    /**
     * @return string|null
     */
    public function getLastDate()
    {
        return ArrayHelper::getValue($this->kurs, 'raspisanie_konec');        
    }

    /**
     * @return ActiveQuery
     */
    private function getQuery()
    {
        if ($this->_query === null)
            $this->_query = $this->createQuery();

        return $this->_query;
    }

    /**
     * @return ActiveQuery
     */
    private function createQuery()
    {
        $query = $this->kurs
            ->getZanyatiya_rel()
            ->where(['>=', 'data', $this->kurs->raspisanie_nachalo])
            ->andWhere(['<=', 'data', $this->kurs->raspisanie_konec])
            ->orderBy(['data' => SORT_ASC, 'nomer' => SORT_ASC]);

        return $query;
    }
}