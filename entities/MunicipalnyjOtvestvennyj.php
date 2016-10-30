<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 30.10.16
 * Time: 16:56
 */

namespace app\entities;

/**
 * Class MunicipalnyjOtvestvennyj
 * @package app\entities
 * @property int id
 * @property int district_id
 * @property int fiz_lico
 */
class MunicipalnyjOtvestvennyj extends EntityBase
{
    public function getFizLicoRel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'fiz_lico']);
    }
}