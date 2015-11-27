<?php
namespace app\enums;

class FormaObucheniya extends EnumBase
{
    const OCHNAYA = 'ochnaya';
    const ZAOCHNAYA = 'zaochnaya';
    const OCHNOZAOCHNAYA = 'ochnozaochnaya';

    public static function namesMap()
    {
        return [
            self::OCHNAYA => 'очная',
            self::ZAOCHNAYA => 'заочная',
            self::OCHNOZAOCHNAYA => 'очно-заочная',
        ];
    }
}
