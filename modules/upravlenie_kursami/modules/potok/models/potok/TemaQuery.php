<?php
namespace app\upravlenie_kursami\potok\models\potok;

use app\base\ActiveQuery;

use app\components\Formatter;

use app\helpers\ArrayHelper;
use app\records\RabotaFizLica;
use app\records\Zanyatie;
use app\records\ZanyatieChastiTemy;
use Yii;
use yii\db\Query;

class TemaQuery extends ActiveQuery
{
    public function setup()
    {
        $columns = [
            't_id' => 'tema.id',
            't_nomer' => 'tema.nomer',
            't_nazvanie' => 'tema.nazvanie',
            't_chasy' => 'tema.chasy',
            't_tip_raboty' => 'rabota_po_teme.nazvanie',
            'prep_id' => 'prepodavatel.id',
            'prep_familiya' => 'prepodavatel.familiya',
            'prep_imya' => 'prepodavatel.imya',
            'prep_otchestvo' => 'prepodavatel.otchestvo',
            'p_id' => 'podrazdel_kursa.id',
            'p_nomer' => 'podrazdel_kursa.nomer',
            'p_nazvanie' => 'podrazdel_kursa.nazvanie',
            'r_id' => 'razdel_kursa.id',
            'r_nomer' => 'razdel_kursa.nomer',
            'r_nazvanie' => 'nazvanie_dlya_razdela_kursa.nazvanie'
        ];

        return $this
            ->select($columns)
            ->leftJoin('rabota_po_teme', 'rabota_po_teme.id = tema.tip_raboty')
            ->leftJoin('fiz_lico prepodavatel', 'prepodavatel.id = tema.prepodavatel_fiz_lico')
            ->leftJoin('podrazdel_kursa', 'podrazdel_kursa.id = tema.podrazdel')
            ->leftJoin('razdel_kursa', 'razdel_kursa.id = podrazdel_kursa.razdel')
            ->leftJoin('nazvanie_dlya_razdela_kursa', 'nazvanie_dlya_razdela_kursa.id = razdel_kursa.nazvanie')
            ->leftJoin('kurs', 'kurs.id = razdel_kursa.kurs');
    }

    public function formatted()
    {
        $order = 'r_nomer, r_id, p_nomer, p_id, t_nomer, t_id';

        $raw = $this
            ->orderBy($order)
            ->asArray()
            ->all();

        $razdely = new RazdelList;

        $lastRid = null;
        $lastPid = null;

        $prepIds = $this->getPrepIds($raw);
        $strukturnyePodrazdeleniya = $this->getStrukturnyePodrazdeleniya($prepIds);

        foreach ($raw as $t) {
            $rid = $t['r_id'];
            if ($rid !== $lastRid) {
                $razdel = [];
                $this->setBaseProperties($razdel, $t, 'r');
                $razdely->addRazdel($razdel);
            }

            $pid = $t['p_id'];
            if ($pid !== $lastPid) {
                $podrazdel = [];
                $this->setBaseProperties($podrazdel, $t, 'p');
                $razdely->addPodrazdel($podrazdel);
            }

            $prepodavatel = $this->getFormattedPrepodavatel($t, $strukturnyePodrazdeleniya);

            $chasy = $t['t_chasy'];
            $chasti = ceil($chasy/2);
            for ($chast = 1; $chast<=$chasti; ++$chast) {
                $chastTemy = [];

                $this->setProperties($chastTemy, $t, 't', [
                    'id',
                    'nomer',
                    'chast' => function() use ($chast) {return $chast;},
                    'nazvanie' => function($value, Formatter $formatter) use ($chast, $chasti) {
                        if ($chasti>1)
                            $value .= " ($chast часть)";

                        return $formatter->asText($value);
                    },
                    'tip_raboty' => 'text',
                    'prepodavatel' => function() use ($prepodavatel) {return $prepodavatel;}
                ]);

                //todo refactor, when introduce chast_temy as entity in db
                $zanyatie = $this->getZanyatie($chastTemy['id'], $chastTemy['chast']);

                if ($zanyatie) {
                    $chastTemy['zanyatie'] = [
                        'id' => $zanyatie->id,
                        'data' => $zanyatie->data
                    ];
                }

                $razdely->addChastTemy($chastTemy);
            }

            $lastPid = $pid;
            $lastRid = $rid;
        }

        return $razdely->toArray();
    }

    private function setBaseProperties(&$item, $from, $prefix)
    {
        $props = [
            'id',
            'nomer',
            'nazvanie' => 'text'
        ];

        $this->setProperties($item, $from, $prefix, $props);
    }

    /**
     * @param $item
     * @param $from
     * @param $prefix
     * @param $props ['nomer', 'nazvanie' => 'text', 'prop' => function ($value, $formatter, $from) {...}]
     */
    private function setProperties(&$item, $from, $prefix, $props)
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        foreach ($props as $prop => $format) {
            if (is_integer($prop)) {
                $prop = $format;
                $format = null;
            }

            $value = ArrayHelper::getValue(
                $from,
                $prefix . '_' . $prop
            );

            if (is_callable($format))
                $formatted = $format($value, $formatter, $from);
            elseif (is_string($format))
                $formatted = $formatter->format($value, $format);
            else
                $formatted = $value;

            $item[$prop] = $formatted;
        }
    }

    private function getZanyatie($tema, $chast)
    {
        $q = ZanyatieChastiTemy::findOne([
            'tema' => $tema,
            'chast_temy' => $chast
        ]);

        return Zanyatie::findOne(['id' => $q]);
    }

    private function getStrukturnyePodrazdeleniya($prepIds)
    {
        $raw = (new Query)
            ->select([
                'id' => 'r.fiz_lico',
                'name' => 'coalesce(sp.sokrashennoe_nazvanie, sp.nazvanie)'
            ])
            ->from('rabota_fiz_lica r')
            ->leftJoin('dolzhnost_fiz_lica_na_rabote d', 'd.rabota_fiz_lica = r.id')
            ->leftJoin('strukturnoe_podrazdelenie sp', 'sp.id = d.strukturnoe_podrazdelenie')
            ->where([
                'and',
                ['r.fiz_lico' => $prepIds],
                'coalesce(sp.sokrashennoe_nazvanie, sp.nazvanie) is not null'
            ])
            ->all();

        $grouped = ArrayHelper::map($raw, 'name', 'name', 'id');
        return $grouped;
    }

    private function getPrepIds($rows)
    {
        $prepIds = ArrayHelper::getColumn($rows, 'prep_id');
        return array_unique($prepIds);
    }

    private function getFormattedPrepodavatel($rawTema, $strukturnyePodrazdeleniya)
    {
        $tmp = [];

        $this->setProperties($tmp, $rawTema, 'prep', [
            'familiya',
            'imya',
            'otchestvo'
        ]);

        $prepId = $rawTema['prep_id'];

        return [
            'fio' => Yii::$app->formatter->asFizLico($tmp),
            'podrazdeleniya' => implode(', ', $strukturnyePodrazdeleniya[$prepId])
        ];
    }
}
