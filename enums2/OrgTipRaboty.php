<?php
namespace app\enums2;

use app\base\BaseEnum;

class OrgTipRaboty extends BaseEnum
{
    const OSNOVNAYA_RABOTA = 'osn';
    const VNUTRENNEE_SOVMESTITELSTVO = 'sovm_vnut';
    const VNESHNEE_SOVMESTITELSTVO = 'sovm_vnesh';

    public static function names()
    {
        return [
            self::OSNOVNAYA_RABOTA => 'основная работа',
            self::VNUTRENNEE_SOVMESTITELSTVO => 'внутреннее совместительство',
            self::VNESHNEE_SOVMESTITELSTVO => 'внешнее совместительство',
        ];
    }
}