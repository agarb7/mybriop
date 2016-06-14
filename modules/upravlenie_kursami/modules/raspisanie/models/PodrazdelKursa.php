<?php
namespace app\upravlenie_kursami\raspisanie\models;

use yii\helpers\ArrayHelper;

use app\records\Tema;

class PodrazdelKursa extends \app\records\PodrazdelKursa
{
    public function getTemy_with_unused_chasti_rel()
    {
        return $this
            ->getTemy_rel()
            ->joinWith('zanyatiya_rel')
            ->groupBy('tema.id')
            ->having('count(zanyatie.chast_temy) * 2 < tema.chasy');
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
        
        $usedChasti = ArrayHelper::getColumn($tema->zanyatiya_rel, 'chast_temy', false);
        
        $result = array_diff($chasti, $usedChasti);
        
        sort($result);
        
        return $result;
    }
}