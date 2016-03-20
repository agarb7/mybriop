<?php
namespace app\modules\plan_prospekt;

use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@app/modules/plan_prospekt/assets';

    public $js = [
        'script.js',
        'kategoriiSlushatelejInputFields.js'
    ];

    public $css = [
        'style.css',
        'kategoriiSlushatelejInputFields.css'
    ];

    public $depends = [
        'yii\widgets\PjaxAsset'
    ];
}