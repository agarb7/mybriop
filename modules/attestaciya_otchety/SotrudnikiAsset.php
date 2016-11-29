<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 28/11/2016
 * Time: 21:33
 */

namespace app\modules\attestaciya_otchety;

use yii\web\AssetBundle;

class SotrudnikiAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/attestaciya_otchety/assets';

    public $js = [
        'sotrudniki.js',
    ];

    public $css = [];

    public $depends = [
        'yii\web\YiiAsset'
    ];
}