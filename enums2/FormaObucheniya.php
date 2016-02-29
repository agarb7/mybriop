<?php
namespace app\enums2;

use app\base\BaseEnum;

class FormaObucheniya extends BaseEnum
{
    const OCHNAYA = 'ochnaya';
    const ZAOCHNAYA = 'zaochnaya';
    const OCHNOZAOCHNAYA = 'ochnozaochnaya';

    public static function names()
    {
        return [
            self::OCHNAYA => 'очная',
            self::ZAOCHNAYA => 'заочная',
            self::OCHNOZAOCHNAYA => 'очно-заочная',
        ];
    }
}
