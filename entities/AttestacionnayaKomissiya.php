<?php

namespace app\entities;
use app\helpers\ArrayHelper;
use app\entities\VremyaProvedeniyaAttestacii;

/**
 * Class AttestacionnayaKomissiya
 * @package app\entities
 *
 * @property int id
 * @property string nazvanie
 * @property int nachalo
 * @property int konec
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

    public static function getKomissiiForDropDown()
    {
        $komissii = static::find()->all();
        return ArrayHelper::map($komissii, 'id', 'nazvanie');
    }
    
    public function getNachaloRel()
    {
        return $this->hasOne(VremyaProvedeniyaAttestacii::className(),['id'=>'nachalo']);
    }

    public function getKonecRel()
    {
        return $this->hasOne(VremyaProvedeniyaAttestacii::className(),['id'=>'konec']);
    }
}