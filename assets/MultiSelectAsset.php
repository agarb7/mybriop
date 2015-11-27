<?php


namespace  app\assets;

use yii\web\AssetBundle;


class MultiSelectAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/multiSelect.js'
    ];

}