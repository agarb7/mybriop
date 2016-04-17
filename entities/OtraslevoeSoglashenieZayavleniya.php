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
        return $this->hasOne(\app\entities\OtraslevoeSoglashenie::className(),['id' => 'otraslevoe_soglashenie']);
    }

    public function getFajlRel(){
        return $this->hasOne(Fajl::className(), ['id' => 'fajl'])
            ->from(Fajl::tableName(). ' otraslevoe_soglashenie_fajl');
    }

    public function getZayavlenieNaAttestaciyuRel(){
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['id' => 'zayavlenie_na_attestaciyu'])->inverseOf('otraslevoeSoglashenieZayavleniyaRel');
    }
}