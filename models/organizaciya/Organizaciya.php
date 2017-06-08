<?php

namespace app\models\organizaciya;

use Yii;
use app\models\vedomstvo\Vedomstvo;
use app\models\adresnyj_objekt\AdresnyjObjekt;
use app\enums\EtapObrazovaniya;
use app\transformers\EnumArrayTransformer;
use app\helpers\SqlArray;

/**
 * This is the model class for table "organizaciya".
 *
 * @property integer $id
 * @property string $nazvanie
 * @property integer $adres_adresnyj_objekt
 * @property string $adres_dom
 * @property array $etapy_obrazovaniya
 * @property boolean $obschij
 * @property integer $vedomstvo
 * @property ObrazovanieDlyaZayavleniyaNaAttestaciyu[] $obrazovanieDlyaZayavleniyaNaAttestaciyus
 * @property ObrazovanieFizLica[] $obrazovanieFizLicas
 * @property AdresnyjObjekt $adresAdresnyjObjekt
 * @property Vedomstvo $vedomstvo0
 * @property RabotaFizLica[] $rabotaFizLicas
 * @property StrukturnoePodrazdelenie[] $strukturnoePodrazdelenies
 * @property ZayavlenieNaAttestaciyu[] $zayavlenieNaAttestaciyus
 * @property ZnachenieIdentifikatora[] $znachenieIdentifikatoras
 */
class Organizaciya extends \app\entities\EntityBase 
{
    /**     
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organizaciya';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nazvanie', 'vedomstvo', 'etapy_obrazovaniya', 'adres_adresnyj_objekt'], 'required'],
            [['adres_adresnyj_objekt', 'vedomstvo'], 'integer'],
            [['obschij'], 'boolean'],
            [['nazvanie'], 'string', 'max' => 400],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nazvanie' => 'Организация',
            'adres_adresnyj_objekt' => 'Город/район',
            'organizaciyaAdres' => 'Город/район',
            'etapy_obrazovaniya' => 'Уровень образовательной организации',
            'etapyObrazovaniyaSpisok' =>'Уровень образовательной организации',
            'obschij' => 'Общедоступный элемент справочника',
            'vedomstvo' => 'Ведомство',
            'vedomstvoNazvanie' => 'Ведомство',
        ];
    }
    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->etapy_obrazovaniya = SqlArray::encode($this->etapy_obrazovaniya, EtapObrazovaniya::className());
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObrazovanieDlyaZayavleniyaNaAttestaciyus()
    {
        return $this->hasMany(ObrazovanieDlyaZayavleniyaNaAttestaciyu::className(), ['organizaciya' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObrazovanieFizLicas()
    {
        return $this->hasMany(ObrazovanieFizLica::className(), ['organizaciya' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdresAdresnyjObjekt()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'adres_adresnyj_objekt']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVedomstvo0()
    {
        return $this->hasOne(Vedomstvo::className(), ['id' => 'vedomstvo']);
    }

    public function getVedomstvoNazvanie()
    {
        return $this->vedomstvo0->nazvanie;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRabotaFizLicas()
    {
        return $this->hasMany(RabotaFizLica::className(), ['organizaciya' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStrukturnoePodrazdelenies()
    {
        return $this->hasMany(StrukturnoePodrazdelenie::className(), ['organizaciya' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZayavlenieNaAttestaciyus()
    {
        return $this->hasMany(ZayavlenieNaAttestaciyu::className(), ['rabota_organizaciya' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZnachenieIdentifikatoras()
    {
        return $this->hasMany(ZnachenieIdentifikatora::className(), ['organizaciya_briop' => 'id']);
    }

    public function transformations()
    {
        return [
            ['etapy_obrazovaniyaAsArray' => 'etapy_obrazovaniya', EnumArrayTransformer::className(), ['enum' => EtapObrazovaniya::className()]]
        ];
    }

    public function getEtapyObrazovaniyaSpisok()
    {
        $str = '';
        foreach ($this->etapy_obrazovaniyaAsArray as $val) 
            {
                if(!empty($val)) {
                    $str = $str.EtapObrazovaniya::getName($val)."; ";
                }
            }
        return $str;
    }

    public function getOrganizaciyaAdres()
    {
        return $this->adresAdresnyjObjekt->oficialnoe_nazvanie;
    }
}
