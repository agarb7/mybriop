<?php
namespace app\widgets;

use yii\web\AssetBundle;

class FieldSwitchAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/assets';

    public $js = ['mybriop.fieldSwitch.js'];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}