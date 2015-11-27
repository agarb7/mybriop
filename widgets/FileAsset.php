<?php


namespace  app\widgets;

use yii\web\AssetBundle;


class FileAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/assets';

    public $css = [
    ];
    public $js = [
        'jquery.form.min.js',
        'files.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

}