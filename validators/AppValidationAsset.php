<?php
namespace app\validators;

use yii\web\AssetBundle;

class AppValidationAsset extends AssetBundle
{
    public $sourcePath = '@app/validators/assets';

    public $js = [
        'mybriop.validation.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];

}