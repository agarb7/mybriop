<?php
namespace app\enums2;

use app\base\BaseEnum;

class StatusKursaFizLica extends BaseEnum
{
    const OZHIDAET_PODTVERZHDENIYA = 'ozhid';
    const ZAPISAN = 'zap';
    const OTMENEN_SLUSHATELEM = 'otm_slush';
    const OTMENEN_BRIOP = 'otm_briop';
    const PROJDEN = 'projd';

    public static function names()
    {
        return [
            self::ZAPISAN => 'записан на курс',
            self::OTMENEN_SLUSHATELEM => 'запись на курс была отменена',
            self::OTMENEN_BRIOP => 'запись на курс была отменена руководителем',
            self::PROJDEN => 'курс успешно пройден',
            self::OZHIDAET_PODTVERZHDENIYA => 'запись ожидает подтверждения'
        ];
    }

}