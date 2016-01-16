<?php
namespace app\widgets;

use yii\web\AssetBundle;

class ComboWidgetAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/assets';

    public $js = ['app.comboWidget.js'];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}