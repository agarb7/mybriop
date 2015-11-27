<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 09.08.15
 * Time: 17:18
 */

namespace app\entities;

/**
 * Class AttestacionnoeVariativnoeIspytanie2
 * @package app\entities
 * @property int $id
 * @property string $nazvanie
 */

class AttestacionnoeVariativnoeIspytanie_2 extends EntityBase
{
    public function getZayavlenieNaAttestaciyuRel(){
        return $this->hasMany(ZayavlenieNaAttestaciyu::className(),['var_ispytanie_2'=>'id'])->inverseOf('attestacionnoeVariativnoeIspytanie2Rel');
    }
}