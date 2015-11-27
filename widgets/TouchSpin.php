<?php
namespace app\widgets;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\validators\NumberValidator;

class TouchSpin extends \kartik\widgets\TouchSpin
{
    public function __construct($config = [])
    {
        $pluginOptions = ['verticalbuttons' => true];

        if (self::configHasModel($config)
            && !isset($config['pluginOptions']['min'])
            && !isset($config['pluginOptions']['max'])
            && !isset($config['pluginOptions']['decimals'])
        ) {
            $validators = $config['model']->getActiveValidators($config['attribute']);
            foreach ($validators as $validator) {
                if ($validator instanceof NumberValidator) {
                    $pluginOptions['min'] = $validator->min;
                    $pluginOptions['max'] = $validator->max;
                    $pluginOptions['decimals'] = $validator->integerOnly ? 0 : 2;
                }
            }
        }

        parent::__construct(ArrayHelper::merge(compact('pluginOptions'), $config));
    }

    private static function configHasModel($config)
    {
        return
            isset($config['model'])
            && $config['model'] instanceof Model
            && isset($config['attribute']);
    }
}