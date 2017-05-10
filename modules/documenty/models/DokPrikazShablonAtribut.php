<?php
namespace app\modules\documenty\models;

use yii\db\ActiveRecord;

class DokPrikazShablonAtribut extends ActiveRecord
{
    public function getShablon()
    {
        return $this->hasOne(DokPrikazShablon::className(), ['id' => 'shablon_id']);
    }
}