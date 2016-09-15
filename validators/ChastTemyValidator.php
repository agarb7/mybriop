<?php
namespace app\validators;

use yii\validators\NumberValidator;

class ChastTemyValidator extends NumberValidator
{
    public function __construct($config = [])
    {
        $config['integerOnly'] = true;
        $config['min'] = 1;
        $config['max'] = 100;

        parent::__construct($config);
    }
}