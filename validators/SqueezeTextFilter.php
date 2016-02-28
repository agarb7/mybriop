<?php
namespace app\validators;

use yii\validators\FilterValidator;

class SqueezeTextFilter extends FilterValidator
{
    public $filter = '\app\helpers\StringHelper::squeezeText';

    public function clientValidateAttribute($model, $attribute, $view)
    {
        AppValidationAsset::register($view);

        return 'value = mybriop.validation.squeezeText($form, attribute);';
    }
}