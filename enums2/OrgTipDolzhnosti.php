<?php
namespace app\enums2;

use app\base\BaseEnum;

class OrgTipDolzhnosti extends BaseEnum
{
    const OSNOVNAYA_DOLZHNOST = 'osn';
    const SOVMESCHENIE = 'sovm';

    public static function names()
    {
        return [
            self::OSNOVNAYA_DOLZHNOST => 'основная должность',
            self::SOVMESCHENIE => 'совмещение'
        ];
    }
}