<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 09.08.15
 * Time: 17:20
 */

namespace app\entities;

/**
 * Class AttestacionnoeVariativnoeIspytanie3
 * @package app\entities
 * @property int $id
 * @property string $nazvanie
 */
class AttestacionnoeVariativnoeIspytanie_3 extends EntityBase
{
    public function getZayavlenieNaAttestaciyuRel(){
        return $this->hasMany(ZayavlenieNaAttestaciyu::className(),['var_ispytanie_3'=>'id'])->inverseOf('attestacionnoeVariativnoeIspytanie3Rel');
    }
}