<?php

namespace app\models\Kurs;

use app\models\Kurs\KursRecord;
use app\models\Kurs\KategoriyaSlushatelya;
use Yii;

/**
 * This is the model class for table "kategoriya_slushatelya".
 *
 * @property integer $id
 * @property string $nazvanie
 *
 * @property KategoriyaSlushatelyaKursa[] $kategoriyaSlushatelyaKursas
 * @property Kurs[] $kurs
 */
class KategoriyaSlushatelyaRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kategoriya_slushatelya';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nazvanie'], 'required'],
            [['nazvanie'], 'string', 'max' => 400]
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKategoriyaSlushatelyaKursas()
    {
        return $this->hasMany(KategoriyaSlushatelyaKursaRecord::className(), ['kategoriya_slushatelya' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKurs()
    {
        return $this->hasMany(KursRecord::className(), ['id' => 'kurs'])->viaTable('kategoriya_slushatelya_kursa', ['kategoriya_slushatelya' => 'id']);
    }
}
