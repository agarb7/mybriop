<?php

namespace app\entities;

/**
 * Class AttestatsionnayaKomissiya
 * @package app\entities
 *
 * @property int id
 * @property string nazvanie
 */

class AttestatsionnayaKomissiya extends EntityBase
{
    public function getRabotnikAttestacionnojKomissiiRel()
    {
        return $this->hasMany(RabotnikAttestacionnojKomissii::className(),['id'=>'attestatsionnaya_komissiya'])->inverseOf('attestatsionnayaKomissiyaRel');
    }

}