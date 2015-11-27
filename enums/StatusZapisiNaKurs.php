<?php
namespace app\enums;

//todo
// 1) rename as in DB;
// 2) rename consts (ZAPISANO, OTMENENO_SLUSHATELEM, OTMENENO_RUKOVODITELEM, PROJDENO)
class StatusZapisiNaKurs extends EnumBase
{
    const ZAPIS = 'zap';
    const OTMENA_ZAPISI = 'otm';
    const OTMENENO_RUKOVODITELEM = 'otm_ruk';

    public static function namesMap()
    {
        return [
            self::ZAPIS => 'запись',
            self::OTMENA_ZAPISI => 'отмена записи',
            self::OTMENENO_RUKOVODITELEM => 'отменено руководителем'
        ];
    }
}