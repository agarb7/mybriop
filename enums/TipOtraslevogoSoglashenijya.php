<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 07.03.16
 * Time: 19:10
 */

namespace app\enums;


class TipOtraslevogoSoglashenijya extends EnumBase
{
    const GOS_NAGRADA = 'gos_nagrada';
    const POCHETNOE_ZVANIE = 'pochetnoe_zvanie';
    const POBEDITEL_KONKURSA = 'pobeditel_konkursa';
    const UCHENAYA_STEPEN = 'uchenaya_stepen';
    const PODGOTOVKA_PRIZEROV_OLIMPIAD = 'podgotovka_prizerov_olimpiad';
    const PODGOTOVKA_PRIZEROV_SOREVNOVANIJ = 'podgotovka_prizerov_sorevnovanij';
    const PROVEDENIE_PROF_EKSPERTIZY = 'provedenie_prof_ekspertizy';

    public static function namesMap()
    {
        return[
            self::GOS_NAGRADA => 'Государственная награда',
            self::POCHETNOE_ZVANIE => 'Почетное звание',
            self::POBEDITEL_KONKURSA => 'Победитель конкурса',
            self::UCHENAYA_STEPEN => 'Ученая степень в течение 5 лет',
            self::PODGOTOVKA_PRIZEROV_OLIMPIAD => 'Подготовка победителей/призеров предметных олимпиад/конкурсов в течение 5 лет',
            self::PODGOTOVKA_PRIZEROV_SOREVNOVANIJ => 'Подготовка призеров соревнований',
            self::PROVEDENIE_PROF_EKSPERTIZY => 'Участие в проведении профессиональной экспетизы в течение 3 лет'
        ];
    }
}