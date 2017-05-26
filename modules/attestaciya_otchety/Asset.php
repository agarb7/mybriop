<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 08.06.16
 * Time: 23:11
 */

namespace app\modules\attestaciya_otchety;


use yii\web\AssetBundle;

class Asset extends AssetBundle
{
    public $sourcePath = '@app/modules/attestaciya_otchety/assets';

    public $js = [
        'itogovyj.js',
        '/js/select2/dist/js/select2.min.js'
    ];

    public $css = [
        '/js/select2/dist/css/select2.min.css'
    ];

    public $depends = [
        'yii\web\YiiAsset'
    ];
}