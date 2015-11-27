<?php

namespace app\enums;


class UrovenAdresnogoObjekta extends EnumBase
{
    const REGION = 'reg';
    const AVTONOMIYA = 'avt_okr';
    const RAJON = 'rajon';
    const GOROD = 'gor';
    const VNUTRIGORODSKOJ_RAJON = 'vnutrigor_ter';
    const NASELYONNYJ_PUNKT = 'nas_punkt';
    const ULICA = 'ul';
    const DOP_TERRITORIYA = 'dop_ter';
    const PODCHINYONNYJ_DOP_TERRITORII = 'pod_dop_ter';

    public static function namesMap()
    {
        return [
            self::REGION => 'reg',
            self::AVTONOMIYA => 'avt_okr',
            self::RAJON => 'rajon',
            self::GOROD => 'gor',
            self::VNUTRIGORODSKOJ_RAJON => 'vnutrigor_ter',
            self::NASELYONNYJ_PUNKT => 'nas_punkt',
            self::ULICA => 'ul',
            self::DOP_TERRITORIYA => 'dop_ter',
            self::PODCHINYONNYJ_DOP_TERRITORII => 'pod_dop_ter'
        ];
    }
}
