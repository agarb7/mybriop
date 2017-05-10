<?php
namespace app\modules\documenty;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@app/modules/documenty/assets';
    
    public $js = [
        'script.js'
    ];

    public $css = [
        'style.css'
    ];

    public $depends = [
    ];
}