<?php

namespace app\models\Kurs;

use app\models\Kurs\KursRecord;
use app\models\Kurs\KategoriyaSlushatelyaRecord;
use Yii;

/**
 * This is the model class for table "kategoriya_slushatelya_kursa".
 *
 * @property integer $kurs
 * @property integer $kategoriya_slushatelya
 *
 * @property Kurs $kurs0
 * @property KategoriyaSlushatelya $kategoriyaSlushatelya
 */
class KategoriyaSlushatelyaKursaRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kategoriya_slushatelya_kursa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kurs', 'kategoriya_slushatelya'], 'required'],
            [['kurs', 'kategoriya_slushatelya'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kurs' => 'Kurs',
            'kategoriya_slushatelya' => 'Kategoriya Slushatelya',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKurs0()
    {
        return $this->hasOne(KursRecord::className(), ['id' => 'kurs']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKategoriyaSlushatelya()
    {
        return $this->hasOne(KategoriyaSlushatelyaRecord::className(), ['id' => 'kategoriya_slushatelya']);
    }
}
