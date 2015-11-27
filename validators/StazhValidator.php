<?php
namespace app\validators;

use yii\validators\NumberValidator;

class StazhValidator extends NumberValidator
{
    public function __construct($config = [])
    {
        $config['integerOnly'] = true;
        $config['min'] = 0;
        $config['max'] = 129;

        parent::__construct($config);
    }
}