<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 10.08.15
 * Time: 15:50
 */

namespace app\entities;

/**
 * Class Kvalifikaciya
 * @package app\entities
 * @property int $id
 * @property string $nazvanie
 * @property bool prisvaevaetsyaBriop
 * @property bool obschij
 */

class Kvalifikaciya extends EntityBase
{
    public function getObrazovaniyaFizLicaRel()
    {
        return $this->hasMany(ObrazovanieFizLica::className(), ['kvalifikaciya' => 'id'])->inverseOf('kvalifikaciyaRel');
    }

    public function getObrazovanieDlyaZayavleniyaNaAttestaciyuRel()
    {
        return $this->hasMany(ObrazovanieDlyaZayavleniyaNaAttestaciyu::className(),['kvalifikaciya'=>'id'])->inverseOf('kvalifikaciyaRel');
    }
}
