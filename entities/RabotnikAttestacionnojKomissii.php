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
 * @property int attestatsionnaya_komissiya reference to attestatsionnaya_komissiya
 * @property bool predsedatel
 */

class RabotnikAttestacionnojKomissii extends EntityBase
{
    public function getAttestatsionnayaKomissiyaRel(){
        return $this->hasOne(AttestatsionnayaKomissiya::className(),['attestatsionnaya_komissiya' => 'id'])->inverseOf('rabotnikAttestacionnojKomissiiRel');
    }
}