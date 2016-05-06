<?php
namespace app\enums2;
use app\base\BaseEnum;
class Rol extends BaseEnum
{
    const ADMINISTRATOR = 'admin';
    const RUKOVODITEL_STRUKTURNOGO_PODRAZDELENIYA = 'ruk_strukt';
    const RUKOVODITEL_KURSOV = 'ruk_kurs';
    const PREPODAVATEL_KURSOV = 'prep_kurs';
    const SOTRUDNIK_UCHEBNOGO_OTDELA = 'uch_otd';
    const REKTOR = 'rek';
    const PROREKTOR_PO_OOD = 'prorek';
    const PEDAGOGICHESKIJ_RABOTNIK = 'ped';
    const RUKOVODITEL_OBRAZOVATELNOGO_UCHREZHDENIYA = 'ruk_org';
    const SOTRUDNIK_OTDELA_ATTESTACII = 'att_otd';
    const RUKOVODITEL_ATTESTACIONNOJ_KOMISSII = 'ruk_att';
    const SOTRUDNIK_ATTESTACIONNOJ_KOMISSII = 'sot_att';
    const SOTRUDNIK_OTDELA_KADROV = 'kadr_otd';

    public static function names()
    {
        return [
            self::ADMINISTRATOR => 'Администратор',
            self::RUKOVODITEL_STRUKTURNOGO_PODRAZDELENIYA => 'Руководитель структурного подразделения',
            self::RUKOVODITEL_KURSOV => 'Руководитель курсов',
            self::PREPODAVATEL_KURSOV => 'Преподаватель курсов',
            self::SOTRUDNIK_UCHEBNOGO_OTDELA => 'Сотрудник Учебного отдела',
            self::REKTOR => 'Ректор',
            self::PROREKTOR_PO_OOD => 'Проректор по ООД',
            self::PEDAGOGICHESKIJ_RABOTNIK => 'Педагогический работник',
            self::RUKOVODITEL_OBRAZOVATELNOGO_UCHREZHDENIYA => 'Руководитель образовательного учреждения',
            self::SOTRUDNIK_OTDELA_ATTESTACII => 'Сотрудник Отдела аттестации',
            self::RUKOVODITEL_ATTESTACIONNOJ_KOMISSII => 'Руководитель аттестационной комиссии',
            self::SOTRUDNIK_ATTESTACIONNOJ_KOMISSII => 'Сотрудник аттестационной комиссии',
            self::SOTRUDNIK_OTDELA_KADROV => 'Сотрудник отдела кадров',
        ];
    }
}