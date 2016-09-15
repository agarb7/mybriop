<?php
namespace app\upravlenie_kursami\potok\models\potok;

use app\components\Formatter;
use app\base\ActiveQuery;

use Yii;

class KursQuery extends ActiveQuery
{
    public function setup()
    {
        $kursColumns = [
            'kurs.id',
            'nazvanie',
            'annotaciya',
            'ochnoe_nachalo',
            'ochnoe_konec',
            'zaochnoe_nachalo',
            'zaochnoe_konec',
            'raschitano_chasov',
            'rukovoditel'
        ];

        $rukColumns = [
            'id',
            'familiya',
            'imya',
            'otchestvo'
        ];

        return $this
            ->joinWith(['rukovoditel_rel' => function (ActiveQuery $q) use ($rukColumns) {
                $q->select($rukColumns);
            }])
            ->select($kursColumns);
    }

    public function formatted()
    {
        /* @var $fmtr Formatter */
        $fmtr = Yii::$app->formatter;

        $oldNullDisplay = $fmtr->nullDisplay;
        $fmtr->nullDisplay = null;

        $result = [];

        foreach ($this->each() as $kurs) {
            /* @var $kurs Kurs */
            $row = [
                'id' => $kurs->id,
                'nazvanie' => $fmtr->asText($kurs->nazvanie),
                'annotaciya' => $fmtr->asParagraphs($kurs->annotaciya),
                'ochnoe' => $this->makeDateRange($kurs->ochnoe_nachalo, $kurs->ochnoe_konec),
                'zaochnoe' => $this->makeDateRange($kurs->zaochnoe_nachalo, $kurs->zaochnoe_konec),
                'raschitano_chasov' => $fmtr->asText($kurs->raschitano_chasov),
                'rukovoditel' => $fmtr->asFizLico($kurs->rukovoditel_rel)
            ];

            $result[] = $row;
        }

        $fmtr->nullDisplay = $oldNullDisplay;

        return $result;
    }

    /**
     * @param $filter KursFilter
     * @return KursQuery
     */
    public function applyFilter($filter)
    {
        $intersectionCond = function ($col1, $col2, $val1, $val2) {
            return [
                'and',
                ['>', $col2, $val1],
                ['<', $col1, $val2]
            ];
        };

        $dateIntersectionCond = [
            'or',
            $intersectionCond('kurs.ochnoe_nachalo', 'kurs.ochnoe_konec', $filter->dateStart, $filter->dateEnd),
            $intersectionCond('kurs.zaochnoe_nachalo', 'kurs.zaochnoe_konec', $filter->dateStart, $filter->dateEnd)
        ];

        $cond = [
            'and',
            ['like', 'kurs.nazvanie', $filter->nazvanie],
            ['kurs.rukovoditel' => $filter->rukovoditelId],
            $dateIntersectionCond,
            ['between', 'kurs.raschitano_chasov', $filter->chasyStart, $filter->chasyEnd]
        ];

        return $this
            ->filterWhere($cond);
    }

    private function makeDateRange($nachalo, $konec)
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        return [
            'nachalo' => $formatter->asDate($nachalo),
            'konec' => $formatter->asDate($konec)
        ];
    }
}