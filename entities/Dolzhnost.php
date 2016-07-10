<?php
namespace app\entities;
use app\entities\settings\ZnachenieIdentifikatora;
use app\enums\TipDolzhnostiEnum;

/**
 * Class Dolzhnost
 * @package app\models\entities
 *
 * @property string $id bigserial NOT NULL,
 * @property string $nazvanie nazvanie NOT NULL,
 * @property string $tip tip_dolzhnosti,
 * @property string $obschij boolean NOT NULL, -- Доступен ли как общий элемент справочника; если false, то запись для единичного использования
 * @property string $rashirenoeNazvanie
 */
class Dolzhnost extends EntityBase
{
    public $rashirenoeNazvanie;

    public static function relations()
    {
        return [
            'dolzhnostiFizLicNaRabotahRel',
            'zayavlenieNaAttestaciyuRel',
            'dolzhnostAttestacionnoiKomissiiRel',
            'stazhiFizLicaRel',
            'znachenieIdentifikatoraDolzhnostiUchitelRel'
        ];
    }

    public function getDolzhnostiFizLicNaRabotahRel()
    {
        return $this
            ->hasMany(DolzhnostFizLicaNaRabote::className(), ['dolzhnost' => 'id'])
            ->inverseOf('dolzhnostRel');
    }

    public function getZayavlenieNaAttestaciyuRel()
    {
        return $this
            ->hasMany(ZayavlenieNaAttestaciyu::className(), ['rabota_dolzhnost' => 'id'])
            ->inverseOf('dolzhnostRel');
    }

    public function getDolzhnostAttestacionnoiKomissiiRel()
    {
        return $this
            ->hasMany(DolzhnostAttestacionnojKomissii::className(), ['dolzhnost' => 'id'])
            ->inverseOf('dolzhnostRel');
    }

    public function getStazhiFizLicaRel()
    {
        return $this
            ->hasMany(StazhFizLica::className(), ['dolzhnost' => 'id'])
            ->inverseOf('dolzhnostRel');
    }

    public function getZnachenieIdentifikatoraDolzhnostiUchitelRel()
    {
        return $this
            ->hasOne(ZnachenieIdentifikatora::className(), ['dolzhnost_uchitel' => 'id'])
            ->inverseOf('dolzhnostUchitelRel');
    }

    public static function getDolzhnostiFizLica($fizLicoId,$withOrganizaciya=false){
        $nazvanie = $withOrganizaciya ? 'dolzhnost.nazvanie||\', \'||organizaciya.nazvanie' : 'dolzhnost.nazvanie';
        $sql = 'select dolzhnost.id,dolzhnost.obschij,dolzhnost.tip,
                       '.$nazvanie.' as nazvanie
                from dolzhnost
                inner join dolzhnost_fiz_lica_na_rabote on dolzhnost.id = dolzhnost_fiz_lica_na_rabote.dolzhnost
                inner join rabota_fiz_lica on rabota_fiz_lica.id = dolzhnost_fiz_lica_na_rabote.rabota_fiz_lica
                inner join organizaciya on rabota_fiz_lica.organizaciya = organizaciya.id
                where rabota_fiz_lica.fiz_lico = :fiz_lico_id';
        return static::findBySql($sql,[':fiz_lico_id'=>$fizLicoId]);
    }

    public static function getDolzhnostFizLica($fizLicoId,$dolzhnostId,$organizaciyaId){
        if (!$fizLicoId)  $fizLicoId = -1;
        if (!$dolzhnostId) $dolzhnostId = -1;
        if (!$organizaciyaId) $organizaciyaId = -1;
        $sql = 'select dolzhnost.* from dolzhnost
                inner join dolzhnost_fiz_lica_na_rabote on dolzhnost.id = dolzhnost_fiz_lica_na_rabote.dolzhnost
                inner join rabota_fiz_lica on rabota_fiz_lica.id = dolzhnost_fiz_lica_na_rabote.rabota_fiz_lica
                inner join organizaciya on rabota_fiz_lica.organizaciya = organizaciya.id
                where rabota_fiz_lica.fiz_lico = :fiz_lico_id and dolzhnost.id = :dolzhnost and organizaciya.id = :organizaciya';
        return static::findBySql($sql,[':fiz_lico_id'=>$fizLicoId,':dolzhnost'=>$dolzhnostId,':organizaciya'=>$organizaciyaId]);
    }

    public static function getObshieDolzhnosti()
    {
        return static::find()->where(['obschij' => true]);
    }

}