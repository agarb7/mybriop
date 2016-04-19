<?php

namespace app\models\vedomstvo;

use Yii;

/**
 * This is the model class for table "vedomstvo".
 *
 * @property integer $id
 * @property string $nazvanie
 * @property string $sokraschyonnoe_nazvanie
 * @property integer $roditel
 *
 * @property Organizaciya[] $organizaciyas
 * @property Vedomstvo $roditel0
 * @property Vedomstvo[] $vedomstvos
 * @property ZnachenieIdentifikatora[] $znachenieIdentifikatoras
 */
class Vedomstvo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vedomstvo';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nazvanie'], 'required'],
            [['roditel'], 'integer'],
            [['nazvanie', 'sokraschyonnoe_nazvanie'], 'string', 'max' => 400]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nazvanie' => 'Nazvanie',
            'sokraschyonnoe_nazvanie' => 'Sokraschyonnoe Nazvanie',
            'roditel' => 'Roditel',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizaciyas()
    {
        return $this->hasMany(Organizaciya::className(), ['vedomstvo' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoditel0()
    {
        return $this->hasOne(Vedomstvo::className(), ['id' => 'roditel']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVedomstvos()
    {
        return $this->hasMany(Vedomstvo::className(), ['roditel' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZnachenieIdentifikatoras()
    {
        return $this->hasMany(ZnachenieIdentifikatora::className(), ['vedomstvo_minobrnauki' => 'id']);
    }
}
