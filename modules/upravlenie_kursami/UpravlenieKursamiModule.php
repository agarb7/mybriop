<?php
namespace app\upravlenie_kursami;

use app\base\Module;

use app\upravlenie_kursami\raspisanie\RaspisanieModule;
use app\upravlenie_kursami\potok\PotokModule;

use app\upravlenie_kursami\models\FizLico;
use app\upravlenie_kursami\models\Kurs;
use app\upravlenie_kursami\models\RabotaFizLica;

use Yii;

class UpravlenieKursamiModule extends Module
{
    public function init()
    {
        parent::init();

        $this->modules = [
            'raspisanie' => RaspisanieModule::className(),
            'potok' => PotokModule::className()
        ];
        
        $this->activeRelationMap = [
            \app\records\FizLico::className() => FizLico::className(),
            \app\records\Kurs::className() => Kurs::className(),
            \app\records\RabotaFizLica::className() => RabotaFizLica::className(),
        ];
    }
}