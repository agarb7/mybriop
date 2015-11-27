<?php

namespace app\entities;

use app\enums\EtapObrazovaniya;
use app\helpers\ArrayHelper;
use Yii;
use yii\db\Query;

/**
 * Class Organizaciya
 * @package app\models\entities
 *
 * @property string $id bigserial NOT NULL,
 * @property string $nazvanie nazvanie NOT NULL,
 * @property string $adresAdresnyjObjekt bigint,
 * @property string $adresDom nomer_stroeniya_pomescheniya,
 * @property string $etapyObrazovaniya etapy_obrazovaniya, -- Поле для фильтрации (или сортировки) при выборе обр. организаций при указании образования пед. работника.
 * @property string $obschij boolean NOT NULL, -- Доступен ли как общий элемент справочника; если false, то запись для единичного использования
 * @property string $vedomstvo bigint, -- Ведомство, к которому относится организация.
 */
class Organizaciya extends EntityBase
{
    public function getRabotyFizLicaRel()
    {
        return $this->hasMany(RabotaFizLica::className(), ['organizaciya' => 'id'])->inverseOf('organizaciyaRel');
    }

    public function getVedomstvoRel()
    {
        return $this->hasOne(Vedomstvo::className(), ['id' => 'vedomstvo'])->inverseOf('organizaciiRel');
    }

    public function getAdresAdresnyjObjektRel()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'adres_adresnyj_objekt'])->inverseOf('organizaciiRel');
    }

    public function getObrazovanieDlyaZayavleniyaNaAttestaciyuRel(){
        return $this->hasMany(ObrazovanieDlyaZayavleniyaNaAttestaciyu::className(),['organizaciya'=>'id'])->inverseOf('organizaciiRel');
    }

    public function getObrazovaniyaFizLicaRel()
    {
        return $this->hasMany(ObrazovanieFizLica::className(), ['organizaciya' => 'id'])->inverseOf('organizaciyaRel');
    }

    public function getZayavlenieNaAttestaciyuRel(){
        return $this->hasMany(ZayavlenieNaAttestaciyu::className(),['rabota_organizaciya'=>'id'])->inverseOf('organizaciyaRel');
    }

    public function getObrazovaniyaDlyaZayavleniyaNaAttestaciyuRel(){
        return $this->hasMany(ObrazovanieDlyaZayavleniyaNaAttestaciyu::className(),['organizaciya'=>'id'])->inverseOf('organizaciyaRel');
    }

    /**
     * @param $vedomstvo_id
     * @param $adres_id
     * @return EntityQuery
     */
    public static function findByVedomstvoAndAdres($vedomstvo_id, $adres_id)
    {
        $vedWhere = [
            'or',
            ['vedomstvo.id' => $vedomstvo_id],
            ['vedomstvo.roditel' => $vedomstvo_id]
        ];

        //todo
        if ($adres_id === '')
            $adres_id = null;

        $aoWhere = [
            'or',
            ['adresnyj_objekt.roditel_urovnya_avtonomii' => $adres_id],
            ['adresnyj_objekt.roditel_urovnya_rajona' => $adres_id],
            ['adresnyj_objekt.roditel_urovnya_goroda' => $adres_id],
            ['adresnyj_objekt.roditel_urovnya_vnutrigorodskogo_rajona' => $adres_id],
            ['adresnyj_objekt.roditel_urovnya_naselyonnogo_punkta' => $adres_id],
            ['adresnyj_objekt.roditel_urovnya_ulicy' => $adres_id],
            ['adresnyj_objekt.roditel_urovnya_dop_territorii' => $adres_id],
            ['adresnyj_objekt.id' => $adres_id]
        ];

        return static::find()
            ->joinWith('vedomstvoRel')->where($vedWhere)
            ->joinWith('adresAdresnyjObjektRel')->andWhere($aoWhere);
    }

    /**
     * @return EntityQuery
     */
    public static function findVysshegoProfessionalnogoObrazovaniya()
    {
        $sql_val = EtapObrazovaniya::asSql(EtapObrazovaniya::VYSSHEE_PROFESSIONALNOE_OBRAZOVANIE);
        $cond = Yii::$app->db->quoteValue($sql_val) . ' = any([[etapy_obrazovaniya]])'; //todo refactor own query with op

        return static::find()->where($cond);
    }

    public static function getVpOrganizaciiWithForFizLico($fiz_lico){
        return static::find()->
            joinWith('obrazovaniyaFizLicaRel')->where([
                'organizaciya.etapy_obrazovaniya'=>'{'.\app\enums\EtapObrazovaniya::VYSSHEE_PROFESSIONALNOE_OBRAZOVANIE.'}'
            ])
            ->orWhere(['obrazovanie_fiz_lica.fiz_lico'=>$fiz_lico])
            ->select(['organizaciya.*']);
    }
}