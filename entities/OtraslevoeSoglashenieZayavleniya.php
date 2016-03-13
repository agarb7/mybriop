<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 08.03.16
 * Time: 19:15
 */

namespace app\entities;

use app\models\attestatsiya\OtraslevoeSoglashenie;

/**
 * Class OtraslevoeSoglashenieZayavleniya
 * @package app\entities
 *
 * @property int id
 * @property int otraslevoeSoglashenie
 * @property int zayavlenieNaAttestaciyu
 * @property int fajl
 */
class OtraslevoeSoglashenieZayavleniya extends EntityBase
{
    public function getOtraslevoeSoglashenieRel()
    {
        return $this->hasOne(OtraslevoeSoglashenie::className(),['id' => 'otraslevoe_soglashenie']);
    }

    public function getFajlRel(){
        return $this->hasOne(Fajl::className(), ['id' => 'fajl']);
    }
}