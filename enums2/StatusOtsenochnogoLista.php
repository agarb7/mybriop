<?php
namespace app\enums2;

use app\base\BaseEnum;

class StatusOtsenochnogoLista extends BaseEnum
{
    const REDAKTITUETSYA = 'redaktiruetsya';
    const ZAPOLNENO = 'zapolneno';

    public static function names()
    {
        return [
            self::REDAKTITUETSYA => 'Редактируется',
            self::ZAPOLNENO => 'Заполнено'
        ];
    }
}