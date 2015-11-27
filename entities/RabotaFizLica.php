<?php

namespace app\entities;
use app\transformers\TelefonTransformer;

/**
 * Class RabotaFizLica
 * @package app\models\entities
 *
 * @property int $id bigserial NOT NULL,
 * @property int $fiz_lico bigint NOT NULL,
 * @property int $organizaciya bigint NOT NULL,
 * @property int $org_tip org_tip_raboty,
 * @property int $dolya_stavki dolya_stavki_zarabotnoj_platy,
 * @property int $telefon telefonnyj_nomer,
 * @property string $formattedTelefon telefonnyj_nomer,
 */
class RabotaFizLica extends EntityBase
{
    public function transformations()
    {
        return [
            ['formattedTelefon' => 'telefon', TelefonTransformer::className()]
        ];
    }

    public function getFizLicoRel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'fiz_lico'])->inverseOf('rabotyFizLicaRel');
    }

    public function getDolzhnostiFizLicaNaRaboteRel()
    {
        return $this->hasMany(DolzhnostFizLicaNaRabote::className(), ['rabota_fiz_lica' => 'id'])->inverseOf('rabotaFizLicaRel');
    }

    public function getOrganizaciyaRel()
    {
        return $this->hasOne(Organizaciya::className(), ['id' => 'organizaciya'])->inverseOf('rabotyFizLicaRel');
    }
}