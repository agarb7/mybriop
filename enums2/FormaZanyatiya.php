<?php
namespace app\enums2;

use app\base\BaseEnum;

class FormaZanyatiya extends BaseEnum
{
    const OCHNAYA = 'ochnaya';
    const EO = 'eo';
    const DOT = 'dot';

    public static function names()
    {
        return [
            self::OCHNAYA => 'очная',
            self::EO => 'электронная без учителя',
            self::DOT => 'дистанционная с учителем'
        ];
    }
}
