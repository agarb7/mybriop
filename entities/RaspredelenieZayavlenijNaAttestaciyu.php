<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 10.01.16
 * Time: 14:37
 */

namespace app\entities;

/**
 * Class RaspredelenieZayavlenijNaAttestaciyu
 * @package app\entities
 *
 * @property int id
 * @property int rabotnik_attestacionnoj_komissii
 * @property int zayavlenie_na_attestaciyu
 */
class RaspredelenieZayavlenijNaAttestaciyu extends EntityBase
{
    public function getZayavlenieNaAttestaciyuRel(){
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['zayavlenie_na_attestaciyu'=>'id'])->inverseOf('raspredelenieZayavlenijNaAttesctaciyuRel');
    }

}