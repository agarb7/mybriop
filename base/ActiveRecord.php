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

    /**
     * @inheritdoc
     */
    public function hasMany($class, $link)
    {
        return parent::hasMany($this->replaceRelatedClass($class), $link);
    }

    /**
     * @inheritdoc
     */
    public function hasOne($class, $link)
    {
        return parent::hasOne($this->replaceRelatedClass($class), $link);
    }

    //todo revision about change wideness, maybe do replace controller wide
    // or first controller, then contains module, then up to parents
    // or remove this feature
    // or utilize DI in controller constructor
    private function replaceRelatedClass($class)
    {
        for ($current = Yii::$app->controller->module; $current !== null; $current = $current->module) {
            if (!$current instanceof Module)
                continue;

            $activeRecordMap = $current->activeRelationMap;

            if (isset($activeRecordMap[$class]))
                return $activeRecordMap[$class];
        }

        return $class;
    }
}