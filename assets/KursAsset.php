<?php

namespace  app\assets;

use yii\web\AssetBundle;

class KursAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/kurs.js'
    ];

}