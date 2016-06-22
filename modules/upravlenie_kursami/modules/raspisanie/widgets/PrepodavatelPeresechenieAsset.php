<?php
namespace app\modules\upravlenie_kursami\modules\raspisanie\widgets;

use yii\web\AssetBundle;

class PrepodavatelPeresechenieAsset extends AssetBundle
{
    public $sourcePath = '@app/upravlenie_kursami/raspisanie/assets';

    public $js = ['prepodavatelPeresechenieModal.js'];

    public $css = ['prepodavatelPeresechenieModal.css'];

    public $depends = [
        'app\upravlenie_kursami\raspisanie\RaspisanieAsset',      
    ];
}