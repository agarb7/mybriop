<?php
namespace app\upravlenie_kursami\raspisanie\widgets;

use yii\web\AssetBundle;

class TemaPickerAsset extends AssetBundle
{
    public $sourcePath = '@app/upravlenie_kursami/raspisanie/assets';

    public $js = ['temaPicker.js'];

    public $css = ['temaPicker.css'];

    public $depends = [        
        'app\upravlenie_kursami\raspisanie\RaspisanieAsset',
        'yii\widgets\PjaxAsset'
    ];    
}