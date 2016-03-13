<?php
namespace app\modules\spisok_slushatelej;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@app/modules/spisok_slushatelej/assets';

    public $js = [
        'script.js'
    ];

    public $css = [
        'style.css'
    ];

    public $depends = [
        'yii\web\YiiAsset'
    ];
}