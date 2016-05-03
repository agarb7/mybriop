<?php
namespace app\records;

use app\base\ActiveRecord;

/**
 * KategoriyaSlushatelya record
 * @property int id
 * @property string nazvanie
 */
class KategoriyaSlushatelya extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "kategoriya_slushatelya";
    }
}
