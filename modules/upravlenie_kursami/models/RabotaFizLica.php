<?php
namespace app\upravlenie_kursami\models;

use app\enums2\OrgTipDolzhnosti;

use app\records\DolzhnostFizLicaNaRabote;

/** 
 * @property DolzhnostFizLicaNaRabote $pervaya_dolzhnost_fiz_lica_na_rabote_rel
 */
class RabotaFizLica extends \app\records\RabotaFizLica
{
    public function getPervaya_dolzhnost_fiz_lica_na_rabote_rel()
    {
        $query = parent::getDolzhnosti_fiz_lica_na_rabote_rel();            

        $query->multiple = false;

        return $query;
    }
}