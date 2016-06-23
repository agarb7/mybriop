<?php
namespace app\enums;

class TipDokumentaObObrazovanii extends EnumBase
{
    const ATTESTAT_OB_OSNOVNOM_OBSCHEM_OBRAZOVANII = 'at_oo';
    const ATTESTAT_O_SREDNEM_OBSCHEM_OBRAZOVANII = 'at_so';
    const SVIDETELSTVO_O_PROFESSII_RABOCHEGO_DOLZHNOSTI_SLUZHASCHEGO = 'sv_prof';
    const DIPLOM_O_SREDNEM_PROFESSIONALNOM_OBRAZOVANII = 'dip_sp';
    const DIPLOM_BAKALAVRA = 'dip_bak';
    const DIPLOM_SPECIALISTA = 'dip_spec';
    const DIPLOM_MAGISTRA = 'dip_mag';
    const DIPLOM = 'dip';
    const UDOSTOVERENIE = 'udost';
    const DIPLOM_O_PROF_PEREPODGOTOVKE = 'dip_pp';

    public static function namesMap(){
        return [
            self::ATTESTAT_OB_OSNOVNOM_OBSCHEM_OBRAZOVANII => 'аттестат об основном общем образовании',
            self::ATTESTAT_O_SREDNEM_OBSCHEM_OBRAZOVANII => 'аттестат о среднем общем образовании',
            self::SVIDETELSTVO_O_PROFESSII_RABOCHEGO_DOLZHNOSTI_SLUZHASCHEGO => 'свидетельство о профессии рабочего, должности служащего',
            self::DIPLOM_O_SREDNEM_PROFESSIONALNOM_OBRAZOVANII => 'диплом о среднем профессиональном образовании',
            self::DIPLOM_BAKALAVRA => 'диплом бакалавра',
            self::DIPLOM_SPECIALISTA => 'диплом специалиста',
            self::DIPLOM_MAGISTRA => 'диплом магистра',
            self::DIPLOM => 'диплом',
            self::UDOSTOVERENIE => 'удостоверение',
            self::DIPLOM_O_PROF_PEREPODGOTOVKE => 'диплом о профессиональной переподготовке'
        ];
    }

    public static function kursTipyMap(){
        $names = self::namesMap();
        return [
            self::DIPLOM => $names[self::DIPLOM],
            self::UDOSTOVERENIE => $names[self::UDOSTOVERENIE],
            self::DIPLOM_O_PROF_PEREPODGOTOVKE => $names[self::DIPLOM_O_PROF_PEREPODGOTOVKE]
        ];
    }
}