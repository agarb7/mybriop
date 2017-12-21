<?php
namespace app\entities\settings;

use app\behaviors\ParolBehavior;
use app\enums\Rol;
use app\transformers\EnumArrayTransformer;

/**
 * Class Masterparol
 * @package app\entities\settings
 * @property string $parol
 * @method boolean validateParol(string $parol)
 * @property array $roliAsArray
 */
class Masterparol extends SettingEntity
{
    public function behaviors()
    {
        return [ParolBehavior::className()];
    }

    public function transformations()
    {
        return [
            ['roliAsArray' => 'roli', EnumArrayTransformer::className(), ['enum' => Rol::className()]]
        ];
    }
}