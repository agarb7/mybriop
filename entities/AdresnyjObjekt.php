<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.07.15
 * Time: 13:13
 */

namespace app\entities;
use app\entities\settings\ZnachenieIdentifikatora;
use app\enums\PriznakCentraAdresnogoObjekta;
use app\enums\UrovenAdresnogoObjekta;

/**
 * Class AdresnyjObjekt
 * @package app\models\entities
 *
 * @property string $id bigserial NOT NULL,
 * @property string $formalnoeNazvanie nazvanie,
 * @property string $oficialnoeNazvanie nazvanie,
 * @property string $uroven uroven_adresnogo_objekta,
 * @property string $priznakCentra priznak_centra_adresnogo_objekta,
 * @property string $pochtovyjIndeks pochtovyj_indeks,
 * @property string $kodIfnsFl kod_sono,
 * @property string $kodTerritorialnogoUchastkaIfnsFl kod_soun,
 * @property string $kodIfnsYul kod_sono,
 * @property string $kodTerritorialnogoUchastkaIfnsYul kod_soun,
 * @property string $okato kod_okato,
 * @property string $oktmo kod_oktmo,
 * @property string $kodRegiona kod_regiona,
 * @property string $kodAvtonomii kod_avtonomii,
 * @property string $kodRajona kod_rajona,
 * @property string $kodGoroda kod_goroda,
 * @property string $kodVnutrigorodskogo_rajona kod_vnutrigorodskogo_rajona,
 * @property string $kodNaselyonnogoPunkta kod_naselyonnogo_punkta,
 * @property string $kodUlicy kod_ulicy,
 * @property string $kodDopTerritorii kod_dop_territorii,
 * @property string $kodPodchinyonnogoDopTerritoriyam kod_podchinyonnogo_dop_territoriyam,
 * @property string $roditel bigint,
 * @property string $roditelUrovnyaRegiona bigint,
 * @property string $roditelUrovnyaAvtonomii bigint,
 * @property string $roditelUrovnyaRajona bigint,
 * @property string $roditelUrovnyaGoroda bigint,
 * @property string $roditelUrovnyaVnutrigorodskogoRajona bigint,
 * @property string $roditelUrovnyaNaselyonnogoPunkta bigint,
 * @property string $roditelUrovnyaUlicy bigint,
 * @property string $roditelUrovnyaDopTerritorii bigint,
 * @property string $fiasAoguid uuid, -- Идентификатор адресного объекта из базы данных ФИАС
 * @property string $obschij boolean NOT NULL, -- Доступен ли как общий элемент справочника; если false, то запись для единичного использования
 * @property string $kladrKod kod_kladr, -- Код адресного объекта из КЛАДР 4.0 одной строкой без признака актуальности (последних двух цифр).
 * @property string $tip bigint, -- Тип адресного объекта.
 */
class AdresnyjObjekt extends EntityBase
{
    public function getOrganizaciiRel()
    {
        return $this->hasMany(Organizaciya::className(), ['adres_adresnyj_objekt' => 'id'])->inverseOf('adresAdresnyjObjektRel');
    }

    public function getMunicipalnyeOtvestvennyeRel()
    {
        return $this->hasMany(MunicipalnyjOtvestvennyj::className(), ['district_id' => 'id']);
    }

    /**
     * @param boolean $includeRegion Include or not Buryatia in resulting set
     * @return EntityQuery
     */
    public static function findBurRajon($includeRegion = false)
    {
        $burRegionId = ZnachenieIdentifikatora::get()->regionBuryatia;

        $where = [
            'and',
            ['roditel_urovnya_regiona' => $burRegionId],
            [
                'or',
                ['uroven' => UrovenAdresnogoObjekta::asSql(UrovenAdresnogoObjekta::RAJON)],
                ['uroven' => UrovenAdresnogoObjekta::asSql(UrovenAdresnogoObjekta::GOROD)]
            ]
        ];

        $query = static::find()->commonOnly()->where($where);

        return $includeRegion
            ? $query->orWhere(['id' => $burRegionId])
            : $query;
    }
}