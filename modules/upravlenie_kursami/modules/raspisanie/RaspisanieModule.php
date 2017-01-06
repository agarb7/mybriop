<?php
namespace app\upravlenie_kursami\raspisanie;

use Yii;

use app\base\Module;

use app\upravlenie_kursami\raspisanie\models\PodrazdelKursa;
use app\upravlenie_kursami\raspisanie\models\Zanyatie;

class RaspisanieModule extends Module
{
    public function init()
    {
        parent::init(); 
        
        $this->activeRelationMap = [            
            \app\records\PodrazdelKursa::className() => PodrazdelKursa::className(),
            \app\records\Zanyatie::className() => Zanyatie::className()
        ];
    }
}