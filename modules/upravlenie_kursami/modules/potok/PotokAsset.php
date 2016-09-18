<?php
namespace app\upravlenie_kursami\potok;

use yii\web\AssetBundle;

class PotokAsset extends AssetBundle
{
    public $sourcePath = '@app/upravlenie_kursami/potok/assets';

    public $css = ['style.css'];

    public $depends = [
        'app\assets\JsviewsAsset'
    ];
}