<?php

namespace app\entities;

/**
 * Class AttestacionnayaKomissiya
 * @package app\entities
 *
 * @property int id
 * @property string nazvanie
 */

class AttestacionnayaKomissiya extends EntityBase
{
    public function getRabotnikAttestacionnojKomissiiRel()
    {
        return $this->hasMany(RabotnikAttestacionnojKomissii::className(),['attestacionnaya_komissiya'=>'id'])->inverseOf('attestacionnayaKomissiyaRel');
    }

    public function getDolzhnostAttestacionnoiKomissiiRel(){
        return $this->hasMany(DolzhnostAttestacionnojKomissii::className(),['attestacionnaya_komissiya'=>'id'])->inverseOf('attestacionnayaKomissiyaRel');
    }
}