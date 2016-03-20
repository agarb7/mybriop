<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 05.09.15
 * Time: 16:41
 */

namespace app\enums;


class StatusZayavleniyaNaAttestaciyu
{
    const REDAKTIRUETSYA_PED_RABOTNIKOM = 'redaktiruetsya_ped_rabotnikom';
    const PODPISANO_PED_RABOTNIKOM = 'podpisano_ped_rabotnikom';
    const OTKLONENO = 'otkloneno';
    const V_OTDELE_ATTESTACII = 'v_otdele_attestacii';

    public static function map()
    {
        return [
            self::REDAKTIRUETSYA_PED_RABOTNIKOM => 'Редактируется педагогическим работником',
            self::PODPISANO_PED_RABOTNIKOM => 'Подписано педагогическим работником',
            self::OTKLONENO => 'Отклонено',
            self::V_OTDELE_ATTESTACII => 'В отделе аттестации'
        ];
    }
}