<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 30.08.15
 * Time: 22:23
 */

namespace app\widgets;


use yii\web\AssetBundle;

class Select3Assets extends AssetBundle
{
    public $sourcePath = '@app/widgets/assets';

    public $css = [
    ];
    public $js = [
        'select3.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}