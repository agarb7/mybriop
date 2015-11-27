<?php
namespace app\entities;

/**
 * Class TekuschayaAttestaciyaFizLica
 * @package app\entities
 * @property integer $fizLico
 * @property integer $attestaciyaFizLica
 */
class TekuschayaAttestaciyaFizLica extends EntityBase
{
    public function getFizLicoRel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'fiz_lico'])->inverseOf('tekuschayaAttestaciyaFizLicaRel');
    }

    public function getAttestaciyaFizLicaRel()
    {
        return $this
            ->hasOne(AttestaciyaFizLica::className(), ['id' => 'attestaciya_fiz_lica'])
            ->inverseOf('tekuschayaAttestaciyaFizLica');
    }
}
