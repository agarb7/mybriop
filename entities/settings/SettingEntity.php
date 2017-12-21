<?php
namespace app\entities\settings;

use app\entities\EntityBase;

class SettingEntity extends EntityBase
{
    /**
     * @return null|static
     */
    public static function get()
    {
        return static::find()->all();
    }
}
