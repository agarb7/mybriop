<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 06.11.2017
 * Time: 22:15
 */

namespace app\enums2;

use app\base\BaseEnum;

class TipDogovoraRaboty extends BaseEnum
{
    const TRUDOVOJ_DOGOVOR = 'trud';
    const DOGOVOR_GRAZHDANSKO_PRAVOVOGO_HARAKTERA = 'gph';

    public static function names()
    {
        return [
            self::TRUDOVOJ_DOGOVOR => 'трудовой договор',
            self::DOGOVOR_GRAZHDANSKO_PRAVOVOGO_HARAKTERA => 'договор гражданско-правового характера',
        ];
    }
}