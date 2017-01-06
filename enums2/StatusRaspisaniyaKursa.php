<?php
namespace app\enums2;

use app\base\BaseEnum;

class StatusRaspisaniyaKursa extends BaseEnum
{
    const REDAKTIRUETSYA = 'redaktiruetsya';
    const ZAVERSHENO = 'zaversheno';

    public static function names()
    {
        return [
            self::REDAKTIRUETSYA => 'Редактируется',
            self::ZAVERSHENO => 'Завершено'
        ];
    }
}
