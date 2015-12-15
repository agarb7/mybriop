<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 29.11.15
 * Time: 20:41
 */

namespace app\entities;

/**
 * Class DolzhnostAttestacionnojKomissii
 * @package app\entities
 *
 * @property int id
 * @property int attestacionnaya_komissiya reference to attestacionnaya_komissiya
 * @property int dolzhnost reference to dolzhnost
 */

class DolzhnostAttestacionnojKomissii extends EntityBase
{
    public function getDolzhnostRel()
    {
        return $this->hasOne(Dolzhnost::className(),['id'=>'dolzhnost'])->inverseOf('dolzhnostAttestacionnoiKomissiiRel');
    }

    public function getAttestacionnayaKomissiyaRel()
    {
        return $this->hasOne(AttestacionnayaKomissiya::className(),['id'=>'attestacionnaya_komissiya'])->inverseOf('dolzhnostAttestacionnoiKomissiiRel');
    }
}