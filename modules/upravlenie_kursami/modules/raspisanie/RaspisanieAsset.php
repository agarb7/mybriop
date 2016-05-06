<?php
namespace app\upravlenie_kursami\raspisanie;

use yii\web\AssetBundle;

class RaspisanieAsset extends AssetBundle
{
    public $sourcePath = '@app/upravlenie_kursami/raspisanie/assets';

    public $css = ['style.css'];

    public $depends = ['app\assets\AppAsset'];
}
