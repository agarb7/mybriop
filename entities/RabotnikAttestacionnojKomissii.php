<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 22.11.15
 * Time: 20:57
 */

namespace app\entities;

/**
 * Class RabotnikAttestacionnojKomissii
 * @package app\entities
 *
 * @property int id
 * @property int fiz_lico reference to fiz_lico
 * @property int attestacionnaya_komissiya reference to attestatsionnaya_komissiya
 * @property bool predsedatel
 */

class RabotnikAttestacionnojKomissii extends EntityBase
{
    public function getAttestacionnayaKomissiyaRel(){
        return $this->hasOne(AttestacionnayaKomissiya::className(),['id' => 'attestacionnaya_komissiya'])->inverseOf('rabotnikAttestacionnojKomissiiRel');
    }

    public function getFizLicoRel(){
        return $this->hasOne(FizLico::className(),['id'=>'fiz_lico'])->inverseOf('rabotnikAttestacionnojKomissiiRel');
    }
}