<?php

namespace app\models\strukturnoe_podrazdelenie;

use Yii;
use app\models\organizaciya\Organizaciya;

/**
 * This is the model class for table "strukturnoe_podrazdelenie".
 *
 * @property integer $id
 * @property integer $organizaciya
 * @property string $nazvanie
 * @property boolean $obschij
 * @property string $sokrashennoe_nazvanie
 *
 * @property DolzhnostFizLicaNaRabote[] $dolzhnostFizLicaNaRabotes
 * @property Kurs[] $kurs
 * @property Organizaciya $organizaciya0
 */
class StrukturnoePodrazdelenie extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'strukturnoe_podrazdelenie';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['organizaciya', 'nazvanie', 'sokrashennoe_nazvanie'], 'required'],
            [['organizaciya'], 'integer'],
            [['obschij'], 'boolean'],
            [['nazvanie', 'sokrashennoe_nazvanie'], 'string', 'max' => 400],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'organizaciya' => 'Организация',
            'nazvanie' => 'Наименование',
            'obschij' => 'Общедоступный элемент справочника',
            'sokrashennoe_nazvanie' => 'Сокращенное название',
            'organizaciyaNazvanie' => 'Организация',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDolzhnostFizLicaNaRabotes()
    {
        return $this->hasMany(DolzhnostFizLicaNaRabote::className(), ['strukturnoe_podrazdelenie' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKurs()
    {
        return $this->hasMany(Kurs::className(), ['strukturnoe_podrazdelenie' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizaciya0()
    {
        return $this->hasOne(Organizaciya::className(), ['id' => 'organizaciya']);
    }

    public function getOrganizaciyaNazvanie()
    {
        return $this->organizaciya0->nazvanie;
    }
}
