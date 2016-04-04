<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 03.04.16
 * Time: 14:05
 */

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