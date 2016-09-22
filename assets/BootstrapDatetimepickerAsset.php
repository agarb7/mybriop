<?php
namespace app\assets;

use yii\web\AssetBundle;

class BootstrapDatetimepickerAsset extends AssetBundle
{
    public $sourcePath = '@bower/eonasdan-bootstrap-datetimepicker';

    public $js = ['build/js/bootstrap-datetimepicker.min.js'];

    public $css = ['build/css/bootstrap-datetimepicker.min.css'];

    public $depends = [
        'yii\web\JqueryAsset',
        'app\assets\MomentAsset'
    ];
}