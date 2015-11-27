<?php
namespace app\validators;

use yii\validators\NumberValidator;

class ChasyObucheniyaValidator extends NumberValidator
{
    public function __construct($config = [])
    {
        $config['integerOnly'] = true;
        $config['min'] = 0;
        $config['max'] = 10000;

        parent::__construct($config);
    }
}