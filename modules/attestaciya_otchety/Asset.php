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
        'itogovyj.js'
    ];

    public $css = [];

    public $depends = [
        'yii\web\YiiAsset'
    ];
}