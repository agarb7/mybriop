<?php
namespace app\entities;

class StazhFizLica extends EntityBase
{
    public function getFizLicoRel()
    {
        $this
            ->hasOne(FizLico::className(), ['id' => 'fiz_lico'])
            ->inverseOf('stazhiFizLicaRel');
    }

    public function getDolzhnostRel()
    {
        return $this
            ->hasOne(Dolzhnost::className(), ['id' => 'dolzhnost'])
            ->inverseOf('stazhiFizLicaRel');
    }
}