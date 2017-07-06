<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 26.06.2017
 * Time: 23:23
 */
namespace app\modules\documenty\enums;

use app\base\BaseEnum;

class Osnovanija extends BaseEnum
{
    const VYPOLNENIE_PLANA = '0';
    const NEVYPOLNENIE_PLANA = '1';
    const NARUSHENIE_PRAVIL = '2';
    const OTSUTSTVIE_OPLATY = '3'; 

    public static function names()
    {
        return [
            self::VYPOLNENIE_PLANA => 'выполнение учебного плана',
            self::NEVYPOLNENIE_PLANA => 'невыполнение учебного плана',
            self::NARUSHENIE_PRAVIL => 'нарушение правил охраны труда и техники безопасности',
            self::OTSUTSTVIE_OPLATY => 'отсутствие оплаты'
        ];
    }
}