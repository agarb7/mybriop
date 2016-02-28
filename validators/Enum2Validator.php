<?php
namespace app\validators;

use app\base\BaseEnum;
use yii\base\InvalidConfigException;
use yii\validators\RangeValidator;
use ReflectionClass;

class Enum2Validator extends RangeValidator
{
    public $strict = true;

    public $enum = null;

    public function init()
    {
        if (!is_string($this->enum))
            throw new InvalidConfigException('The "enumClass" property must be set.');

        /* @var $class BaseEnum */
        $class = $this->enum;
        $this->range = $class::items();

        parent::init();
    }
}