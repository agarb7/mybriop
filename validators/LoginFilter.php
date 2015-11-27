<?php
namespace app\validators;

use yii\helpers\ArrayHelper;
use yii\validators\FilterValidator;
use yii\validators\ValidationAsset;

class LoginFilter extends FilterValidator
{
    public function __construct($config = [])
    {
        $filter = function ($value) {
            return trim(mb_strtolower($value));
        };

        parent::__construct(ArrayHelper::merge($config, compact('filter')));
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        ValidationAsset::register($view);
        AppValidationAsset::register($view);

        return 'value = yii.validation.trim($form, attribute, []);'
            . 'value = mybriop.validation.toLower($form, attribute);';
    }
}
