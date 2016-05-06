<?php
namespace app\enums2;

use app\base\BaseEnum;

class StatusProgrammyKursa extends BaseEnum
{
    const REDAKTIRUETSYA = 'redaktiruetsya';
    const ZAVERSHENA = 'zavershena';

    public static function names()
    {
        return [
            self::REDAKTIRUETSYA => 'Редактируется',
            self::ZAVERSHENA => 'Завершена'
        ];
    }
}
