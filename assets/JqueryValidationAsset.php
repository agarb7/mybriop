<?php
namespace app\assets;

use yii\web\AssetBundle;

class JqueryValidationAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-validation';

    public $js = [
        'dist/jquery.validate.min.js',
        'src/localization/messages_ru.js'
    ];

    public $depends = ['yii\web\JqueryAsset'];
}
