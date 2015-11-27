<?php

namespace app\enums;

class KategoriyaPedRabotnika extends EnumBase
{
    const BEZ_KATEGORII = 'bez_kategorii';
    const PERVAYA_KATEGORIYA = 'pervaya_kategoriya';
    const VYSSHAYA_KATEGORIYA = 'vyshaya_kategoriya';

    public static function namesMap()
    {
        return [
            self::BEZ_KATEGORII => 'без категории',
            self::PERVAYA_KATEGORIYA => 'первая категория',
            self::VYSSHAYA_KATEGORIYA => 'высшая категория'
        ];
    }

    public static function namesOnlyPositive()
    {
        return [
            self::PERVAYA_KATEGORIYA => 'первая категория',
            self::VYSSHAYA_KATEGORIYA => 'высшая категория'
        ];
    }
}