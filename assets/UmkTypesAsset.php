<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 19.03.15
 * Time: 17:28
 */

namespace app\assets;


use yii\web\AssetBundle;

class UmkTypesAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/umkType.js'
    ];
}