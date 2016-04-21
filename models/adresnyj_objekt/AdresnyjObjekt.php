<?php

namespace app\models\adresnyj_objekt;

use Yii;

/**
 * This is the model class for table "adresnyj_objekt".
 *
 * @property integer $id
 * @property string $formalnoe_nazvanie
 * @property string $oficialnoe_nazvanie
 * @property string $uroven
 * @property string $priznak_centra
 * @property string $pochtovyj_indeks
 * @property string $kod_ifns_fl
 * @property string $kod_territorialnogo_uchastka_ifns_fl
 * @property string $kod_ifns_yul
 * @property string $kod_territorialnogo_uchastka_ifns_yul
 * @property string $okato
 * @property string $oktmo
 * @property string $kod_regiona
 * @property string $kod_avtonomii
 * @property string $kod_rajona
 * @property string $kod_goroda
 * @property string $kod_vnutrigorodskogo_rajona
 * @property string $kod_naselyonnogo_punkta
 * @property string $kod_ulicy
 * @property string $kod_dop_territorii
 * @property string $kod_podchinyonnogo_dop_territoriyam
 * @property integer $roditel
 * @property integer $roditel_urovnya_regiona
 * @property integer $roditel_urovnya_avtonomii
 * @property integer $roditel_urovnya_rajona
 * @property integer $roditel_urovnya_goroda
 * @property integer $roditel_urovnya_vnutrigorodskogo_rajona
 * @property integer $roditel_urovnya_naselyonnogo_punkta
 * @property integer $roditel_urovnya_ulicy
 * @property integer $roditel_urovnya_dop_territorii
 * @property string $fias_aoguid
 * @property boolean $obschij
 * @property string $kladr_kod
 * @property integer $tip
 *
 * @property AdresnyjObjekt $roditel0
 * @property AdresnyjObjekt[] $adresnyjObjekts
 * @property AdresnyjObjekt $roditelUrovnyaRegiona
 * @property AdresnyjObjekt[] $adresnyjObjekts0
 * @property AdresnyjObjekt $roditelUrovnyaAvtonomii
 * @property AdresnyjObjekt[] $adresnyjObjekts1
 * @property AdresnyjObjekt $roditelUrovnyaRajona
 * @property AdresnyjObjekt[] $adresnyjObjekts2
 * @property AdresnyjObjekt $roditelUrovnyaGoroda
 * @property AdresnyjObjekt[] $adresnyjObjekts3
 * @property AdresnyjObjekt $roditelUrovnyaVnutrigorodskogoRajona
 * @property AdresnyjObjekt[] $adresnyjObjekts4
 * @property AdresnyjObjekt $roditelUrovnyaNaselyonnogoPunkta
 * @property AdresnyjObjekt[] $adresnyjObjekts5
 * @property AdresnyjObjekt $roditelUrovnyaUlicy
 * @property AdresnyjObjekt[] $adresnyjObjekts6
 * @property AdresnyjObjekt $roditelUrovnyaDopTerritorii
 * @property AdresnyjObjekt[] $adresnyjObjekts7
 * @property TipAdresnogoObjekta $tip0
 * @property FizLico[] $fizLicos
 * @property Organizaciya[] $organizaciyas
 * @property ZnachenieIdentifikatora[] $znachenieIdentifikatoras
 * @property ZnachenieIdentifikatora[] $znachenieIdentifikatoras0
 */
class AdresnyjObjekt extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adresnyj_objekt';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uroven', 'priznak_centra', 'kod_regiona', 'kod_avtonomii', 'kod_rajona', 'kod_goroda', 'kod_vnutrigorodskogo_rajona', 'kod_naselyonnogo_punkta', 'kod_ulicy', 'kod_dop_territorii', 'kod_podchinyonnogo_dop_territoriyam', 'fias_aoguid'], 'string'],
            [['roditel', 'roditel_urovnya_regiona', 'roditel_urovnya_avtonomii', 'roditel_urovnya_rajona', 'roditel_urovnya_goroda', 'roditel_urovnya_vnutrigorodskogo_rajona', 'roditel_urovnya_naselyonnogo_punkta', 'roditel_urovnya_ulicy', 'roditel_urovnya_dop_territorii', 'tip'], 'integer'],
            [['obschij'], 'required'],
            [['obschij'], 'boolean'],
            [['formalnoe_nazvanie', 'oficialnoe_nazvanie'], 'string', 'max' => 400],
            [['pochtovyj_indeks'], 'string', 'max' => 6],
            [['kod_ifns_fl', 'kod_territorialnogo_uchastka_ifns_fl', 'kod_ifns_yul', 'kod_territorialnogo_uchastka_ifns_yul'], 'string', 'max' => 4],
            [['okato', 'oktmo'], 'string', 'max' => 11],
            [['kladr_kod'], 'string', 'max' => 15]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'formalnoe_nazvanie' => 'Formalnoe Nazvanie',
            'oficialnoe_nazvanie' => 'Oficialnoe Nazvanie',
            'uroven' => 'Uroven',
            'priznak_centra' => 'Priznak Centra',
            'pochtovyj_indeks' => 'Pochtovyj Indeks',
            'kod_ifns_fl' => 'Kod Ifns Fl',
            'kod_territorialnogo_uchastka_ifns_fl' => 'Kod Territorialnogo Uchastka Ifns Fl',
            'kod_ifns_yul' => 'Kod Ifns Yul',
            'kod_territorialnogo_uchastka_ifns_yul' => 'Kod Territorialnogo Uchastka Ifns Yul',
            'okato' => 'Okato',
            'oktmo' => 'Oktmo',
            'kod_regiona' => 'Kod Regiona',
            'kod_avtonomii' => 'Kod Avtonomii',
            'kod_rajona' => 'Kod Rajona',
            'kod_goroda' => 'Kod Goroda',
            'kod_vnutrigorodskogo_rajona' => 'Kod Vnutrigorodskogo Rajona',
            'kod_naselyonnogo_punkta' => 'Kod Naselyonnogo Punkta',
            'kod_ulicy' => 'Kod Ulicy',
            'kod_dop_territorii' => 'Kod Dop Territorii',
            'kod_podchinyonnogo_dop_territoriyam' => 'Kod Podchinyonnogo Dop Territoriyam',
            'roditel' => 'Roditel',
            'roditel_urovnya_regiona' => 'Roditel Urovnya Regiona',
            'roditel_urovnya_avtonomii' => 'Roditel Urovnya Avtonomii',
            'roditel_urovnya_rajona' => 'Roditel Urovnya Rajona',
            'roditel_urovnya_goroda' => 'Roditel Urovnya Goroda',
            'roditel_urovnya_vnutrigorodskogo_rajona' => 'Roditel Urovnya Vnutrigorodskogo Rajona',
            'roditel_urovnya_naselyonnogo_punkta' => 'Roditel Urovnya Naselyonnogo Punkta',
            'roditel_urovnya_ulicy' => 'Roditel Urovnya Ulicy',
            'roditel_urovnya_dop_territorii' => 'Roditel Urovnya Dop Territorii',
            'fias_aoguid' => 'Fias Aoguid',
            'obschij' => 'Obschij',
            'kladr_kod' => 'Kladr Kod',
            'tip' => 'Tip',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditel0()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'roditel']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresnyjObjekts()
    {
        return $this->hasMany(AdresnyjObjekt::className(), ['roditel' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditelUrovnyaRegiona()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'roditel_urovnya_regiona']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresnyjObjekts0()
    {
        return $this->hasMany(AdresnyjObjekt::className(), ['roditel_urovnya_regiona' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditelUrovnyaAvtonomii()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'roditel_urovnya_avtonomii']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresnyjObjekts1()
    {
        return $this->hasMany(AdresnyjObjekt::className(), ['roditel_urovnya_avtonomii' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditelUrovnyaRajona()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'roditel_urovnya_rajona']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresnyjObjekts2()
    {
        return $this->hasMany(AdresnyjObjekt::className(), ['roditel_urovnya_rajona' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditelUrovnyaGoroda()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'roditel_urovnya_goroda']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresnyjObjekts3()
    {
        return $this->hasMany(AdresnyjObjekt::className(), ['roditel_urovnya_goroda' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditelUrovnyaVnutrigorodskogoRajona()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'roditel_urovnya_vnutrigorodskogo_rajona']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresnyjObjekts4()
    {
        return $this->hasMany(AdresnyjObjekt::className(), ['roditel_urovnya_vnutrigorodskogo_rajona' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditelUrovnyaNaselyonnogoPunkta()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'roditel_urovnya_naselyonnogo_punkta']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresnyjObjekts5()
    {
        return $this->hasMany(AdresnyjObjekt::className(), ['roditel_urovnya_naselyonnogo_punkta' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditelUrovnyaUlicy()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'roditel_urovnya_ulicy']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresnyjObjekts6()
    {
        return $this->hasMany(AdresnyjObjekt::className(), ['roditel_urovnya_ulicy' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditelUrovnyaDopTerritorii()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'roditel_urovnya_dop_territorii']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresnyjObjekts7()
    {
        return $this->hasMany(AdresnyjObjekt::className(), ['roditel_urovnya_dop_territorii' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTip0()
    {
        return $this->hasOne(TipAdresnogoObjekta::className(), ['id' => 'tip']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFizLicos()
    {
        return $this->hasMany(FizLico::className(), ['propiska_adresnyj_objekt' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizaciyas()
    {
        return $this->hasMany(Organizaciya::className(), ['adres_adresnyj_objekt' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZnachenieIdentifikatoras()
    {
        return $this->hasMany(ZnachenieIdentifikatora::className(), ['region_buryatia' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZnachenieIdentifikatoras0()
    {
        return $this->hasMany(ZnachenieIdentifikatora::className(), ['gorod_ulan_ude' => 'id']);
    }
}
