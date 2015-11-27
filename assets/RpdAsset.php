<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 07.06.15
 * Time: 20:02
 */

namespace app\assets;

use yii\web\AssetBundle;

class RpdAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/redaktor_rpd.js'
    ];

}