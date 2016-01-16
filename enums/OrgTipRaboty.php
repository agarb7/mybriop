<?php
namespace app\enums;

class OrgTipRaboty extends EnumBase
{
    const OSN = 'osn';
    const SOVM_VNUT = 'sovm_vnut';
    const SOVM_VNESH = 'sovm_vnesh';

    public static function namesMap()
    {
        return [
            self::OSN => 'основная работа',
            self::SOVM_VNUT => 'внутреннее совместительство',
            self::SOVM_VNESH => 'внешнее совместительство'
        ];
    }
}