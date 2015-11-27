<?php
namespace app\validators;

use yii\base\InvalidConfigException;
use yii\validators\RangeValidator;
use ReflectionClass;

class EnumValidator extends RangeValidator
{
    public $strict = true;

    public $enumClass = null;

    public function init()
    {
        if (!is_string($this->enumClass))
            throw new InvalidConfigException('The "enumClass" property must be set.');

        $consts = (new ReflectionClass($this->enumClass))->getConstants();
        $this->range = array_values($consts);

        parent::init();
    }
}