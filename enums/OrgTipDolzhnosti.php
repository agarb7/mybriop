<?php
namespace app\enums;

class OrgTipDolzhnosti extends EnumBase
{
    const OSN = 'osn';
    const SOVM = 'sovm';

    public static function namesMap()
    {
        return [
            self::OSN => 'основная должность',
            self::SOVM => 'совмещение'
        ];
    }
}