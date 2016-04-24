<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 24.04.16
 * Time: 22:53
 */

namespace app\enums2;


use app\base\BaseEnum;

class StatusOtsenokZayavleniya extends BaseEnum
{
    const REDAKTIRUETSYA = 'redaktiruetsya';
    const PODPISANO = 'podpisano';

    public static function names(){
        return[
            self::REDAKTIRUETSYA => 'Редактируется',
            self::PODPISANO => 'Подписано'
        ];
    }
}