<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 06.02.16
 * Time: 12:20
 */

namespace app\enums;


class StatusProgrammyKursa extends EnumBase
{
    const REDAKTIRUETSYA = 'redaktiruetsya';
    const ZAVERSHENA = 'zavershena';

    public static function namesMap()
    {
        return [
            self::REDAKTIRUETSYA => 'Редактируется',
            self::ZAVERSHENA => 'Подписана',
        ];
    }
}