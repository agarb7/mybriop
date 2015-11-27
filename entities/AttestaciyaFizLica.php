<?php
namespace app\entities;

/**
 * Class AttestaciyaFizLica
 * @package app\entities
 *
 * @property int $id
 * @property int $fizLico
 * @property int $dolzhnostFizLicaNaRabote
 * @property strign $kategoriya
 * @property string $dataPrisvoeniya
 * @property string $dataOkonchaniyaDejstviya
 * @property string $dataOtkaza
 * @property string $attestacionnyjListNomer
 */

class AttestaciyaFizLica extends EntityBase
{
    public function getTekuschayaAttestaciyaFizLica()
    {
        return $this
            ->hasOne(TekuschayaAttestaciyaFizLica::className(), ['attestaciya_fiz_lica' => 'id'])
            ->inverseOf('attestaciyaFizLicaRel');
    }
}