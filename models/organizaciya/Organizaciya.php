<?php

namespace app\models\organizaciya;

use Yii;
use app\models\vedomstvo\Vedomstvo;
use app\enums\EtapObrazovaniya;
use app\transformers\EnumArrayTransformer;

/**
 * This is the model class for table "organizaciya".
 *
 * @property integer $id
 * @property string $nazvanie
 * @property integer $adres_adresnyj_objekt
 * @property string $adres_dom
 * @property string $etapy_obrazovaniya
 * @property boolean $obschij
 * @property integer $vedomstvo
 *
 * @property ObrazovanieDlyaZayavleniyaNaAttestaciyu[] $obrazovanieDlyaZayavleniyaNaAttestaciyus
 * @property ObrazovanieFizLica[] $obrazovanieFizLicas
 * @property AdresnyjObjekt $adresAdresnyjObjekt
 * @property Vedomstvo $vedomstvo0
 * @property RabotaFizLica[] $rabotaFizLicas
 * @property StrukturnoePodrazdelenie[] $strukturnoePodrazdelenies
 * @property ZayavlenieNaAttestaciyu[] $zayavlenieNaAttestaciyus
 * @property ZnachenieIdentifikatora[] $znachenieIdentifikatoras
 */
class Organizaciya extends \app\entities\EntityBase //\yii\db\ActiveRecord
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
            [['nazvanie', 'obschij', 'vedomstvoNazvanie'], 'required'],
            [['adres_adresnyj_objekt', 'vedomstvo'], 'integer'],
            [['etapy_obrazovaniya'], 'string'],
            [['obschij'], 'boolean'],
            [['nazvanie'], 'string', 'max' => 400],
            [['adres_dom'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nazvanie' => 'Организация',
            'adres_adresnyj_objekt' => 'Adres Adresnyj Objekt',
            'adres_dom' => 'Adres Dom',
            'etapy_obrazovaniya' => 'Уровень образовательной организации',
            'obschij' => 'Общедоступный элемент справочника',
            'vedomstvo' => 'Ведомство',
            'vedomstvoNazvanie' => 'Ведомство',
        ];
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
}
