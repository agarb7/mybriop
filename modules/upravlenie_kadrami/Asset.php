<?php
namespace app\modules\upravlenie_kadrami;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@app/modules/upravlenie_kadrami/assets';
    
    public $js = [
        'script.js',
        '/js/select2/dist/js/select2.min.js'
    ];

    public $css = [
        'style.css',
        '/js/select2/dist/css/select2.min.css'
    ];

    public $depends = [
    ];
}