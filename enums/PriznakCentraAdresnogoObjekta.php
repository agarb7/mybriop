<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.07.15
 * Time: 14:59
 */

namespace app\enums;

class PriznakCentraAdresnogoObjekta extends EnumBase
{
    const NE_CENTR = 'ne_centr';
    const CENTR_RAJONA = 'centr_rajona';
    const CENTR_REGIONA = 'centr_regiona';
    const CENTR_REGIONA_I_RAJONA = 'centr_regiona_i_rajona';
    const CENTRALNYJ_RAJON = 'centralnyj_rajon';

    public static function namesMap()
    {
        return [
            self::NE_CENTR => 'не центр административно-территориального образования',
            self::CENTR_RAJONA => 'центр района',
            self::CENTR_REGIONA => 'центр (столица) региона',
            self::CENTR_REGIONA_I_RAJONA => 'центр района и центр региона',
            self::CENTRALNYJ_RAJON => 'центральный район — район с центром региона'
        ];
    }
}