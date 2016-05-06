<?php
namespace app\base;

use Yii;

class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @return ActiveQuery     
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }
}