<?php
namespace app\validators;

use yii\validators\FilterValidator;

class SqueezeLineFilter extends FilterValidator
{
    public $filter = '\app\helpers\StringHelper::squeezeLine';

    public function clientValidateAttribute($model, $attribute, $view)
    {
        AppValidationAsset::register($view);

        return 'value = mybriop.validation.squeezeLine($form, attribute);';
    }
}