<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 28/11/2016
 * Time: 21:33
 */

namespace app\modules\attestaciya_otchety;

use yii\web\AssetBundle;

class DolzhnostiAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/attestaciya_otchety/assets';

    public $js = [
        'dolzhnosti.js',
    ];

    public $css = [];

    public $depends = [
        'yii\web\YiiAsset'
    ];
}