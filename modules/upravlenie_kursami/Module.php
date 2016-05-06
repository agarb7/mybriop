<?php
namespace app\upravlenie_kursami;

use app\upravlenie_kursami\raspisanie\RaspisanieModule;

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->modules = [
            'raspisanie' => RaspisanieModule::className()
        ];
    }
}