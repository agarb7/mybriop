<?php
namespace app\upravlenie_kursami\raspisanie\models;

use yii\helpers\ArrayHelper;

use app\records\Tema;

class TemaFilter extends Tema
{
    /**
     * @var integer Strukturnoe podrazdelenie of prepodavatel ID
     */
    public $prepodavatel_strukturnoe_podrazdelenie;

    public function rules()
    {
        return [[
            [
                'podrazdel',
                'prepodavatel_fiz_lico',
                'prepodavatel_strukturnoe_podrazdelenie',
                'nedelya',
            ],
            'default'
        ]];
    }

    /**
     * @param PodrazdelKursa $podrazdel
     * @return boolean
     */
    public function filterPodrazdel($podrazdel)
    {
        return $this->filterMatch($podrazdel->id, $this->podrazdel);
    }

    /**
     * @param Tema $tema
     * @return boolean
     */
    public function filterTema($tema)
    {
        return
            $this->filterMatch($tema->prepodavatel_fiz_lico, $this->prepodavatel_fiz_lico)
            && $this->filterMatch(
                ArrayHelper::getValue($tema, 'prepodavatel_fiz_lico_rel.pervoe_strukturnoe_podrazdelenie_briop.id'),
                $this->prepodavatel_strukturnoe_podrazdelenie)
            && $this->filterMatch($tema->nedelya, $this->nedelya);
    }

    /**
     * @param $value
     * @param $filter
     * @return bool
     */
    private function filterMatch($value, $filter)
    {
        if ($filter === null)
            return true;

        return $value == $filter;
    }
}