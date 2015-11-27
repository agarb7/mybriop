<?php
namespace app\entities\settings;

use app\behaviors\ParolBehavior;

/**
 * Class Masterparol
 * @package app\entities\settings
 * @property string $parol
 * @method boolean validateParol(string $parol)
 */
class Masterparol extends SettingEntity
{
    public function behaviors()
    {
        return [ParolBehavior::className()];
    }
}