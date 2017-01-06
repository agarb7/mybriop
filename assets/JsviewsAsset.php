<?php
namespace app\assets;

use yii\web\AssetBundle;

class JsviewsAsset extends AssetBundle
{
    public $sourcePath = '@bower/jsviews';

    public $js = ['jsviews.min.js'];

    public $depends = ['yii\web\JqueryAsset'];
}