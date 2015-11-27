<?php
namespace app\entities;
use app\transformers\EnumTransformer;
use app\enums\EtapObrazovaniya;

/**
 * Class DolzhnostFizLicaNaRabote
 * @package app\models\entities
 *
 * @property string $id bigserial NOT NULL,
 * @property string $rabota_fiz_lica bigint NOT NULL,
 * @property string $strukturnoe_podrazdelenie bigint,
 * @property string $rukovoditel_strukturnogo_podrazdeleniya boolean, -- Признак руководителя данного структурного подразделения.
 * @property string $dolzhnost bigint,
 * @property string $org_tip org_tip_dolzhnosti,
 * @property string $etap_obrazovaniya etap_obrazovaniya,
 * @property string $stazh stazh,
 *
 * @property string $etapObrazovaniyaAsEnum etap_obrazovaniya,
 */
class DolzhnostFizLicaNaRabote extends EntityBase
{
    public function transformations()
    {
        return [
            ['etapObrazovaniyaAsEnum' => 'etapObrazovaniya', EnumTransformer::className(), ['enum' => EtapObrazovaniya::className()]]
        ];
    }

    public function getRabotaFizLicaRel()
    {
        return $this->hasOne(RabotaFizLica::className(), ['id' => 'rabota_fiz_lica'])->inverseOf('dolzhnostiFizLicaNaRaboteRel');
    }

    public function getDolzhnostRel()
    {
        return $this->hasOne(Dolzhnost::className(), ['id' => 'dolzhnost'])->inverseOf('dolzhnostiFizLicNaRabotahRel');
    }
}