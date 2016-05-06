<?php
namespace app\upravlenie_kursami\raspisanie\widgets;

use yii\web\AssetBundle;

class ZanyatieGridAsset extends AssetBundle
{
    public $sourcePath = '@app/upravlenie_kursami/raspisanie/assets';

    public $js = ['zanyatieGrid.js'];

    public $css = ['zanyatieGrid.css'];

    public $depends = ['app\upravlenie_kursami\raspisanie\RaspisanieAsset'];
}