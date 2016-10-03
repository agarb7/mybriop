<?php
namespace app\upravlenie_kursami\raspisanie\models;

use yii\db\Query;
use yii\helpers\ArrayHelper;

use app\records\Tema;

class PodrazdelKursa extends \app\records\PodrazdelKursa
{
    public function getTemy_with_unused_chasti_rel()
    {
        $actualZanyatiya = (new Query)
            ->select([
                'zct.tema',
                'zct.chast_temy'
            ])
            ->from('zanyatie_chasti_temy zct')
            ->leftJoin('zanyatie z', 'z.id = zct.zanyatie')
            ->where($this->zanyatieIsUsedCond('z'));

        return $this
            ->getTemy_rel()
            ->leftJoin(['az' => $actualZanyatiya], 'az.tema = tema.id')
            ->groupBy('tema.id')
            ->having('count(az.chast_temy) * 2 < tema.chasy');
    }

    public function getUnused_chasti_tem()
    {
        $result = [];

        foreach ($this->temy_rel as $tema) {
            /* @var $tema Tema */            
            $chasti = $this->getUnusedChasti($tema);
            
            foreach ($chasti as $chast) {
                $result[] = new ChastTemy([
                    'tema' => $tema,
                    'chast' => $chast
                ]);
            }
        }

        usort($result, function ($a,$b) {
            return $a->tema->nomer - $b->tema->nomer;
        });

        return $result;
    }

    /**
     * @param Tema $tema
     * @return integer[]
     */
    private function getUnusedChasti($tema)
    {
        $chasy = $tema->chasy ?: 2;
        $chastiCount = ceil($chasy / 2);
        
        $chasti = range(1, $chastiCount);

        $zcts = $tema
            ->getZanyatiya_chastej_tem_rel()
            ->joinWith('zanyatie_rel')
            ->where($this->zanyatieIsUsedCond())
            ->all();
        
        $usedChasti = ArrayHelper::getColumn($zcts, 'chast_temy', false);
        
        $result = array_diff($chasti, $usedChasti);
        
        sort($result);
        
        return $result;
    }

    private function zanyatieIsUsedCond($tableName = null)
    {
        if ($tableName === null)
            $tableName = 'zanyatie';

        return [
            'not',
            [
                "{{{$tableName}}}.[[data]]" => null,
                "{{{$tableName}}}.[[nomer]]" => null
            ]
        ];
    }
}