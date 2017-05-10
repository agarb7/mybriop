<?php
namespace app\enums2;

use app\base\BaseEnum;

class TipDolzhnosti extends BaseEnum
{
    const UCHITEL_PREPODAVATEL = 'uchprep';

    public static function names()
    {
        return [
            self::UCHITEL_PREPODAVATEL => 'учитель/преподаватель',
        ];
    }
}