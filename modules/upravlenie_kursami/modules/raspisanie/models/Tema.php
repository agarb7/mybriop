<?php
namespace app\upravlenie_kursami\raspisanie\models;

use app\upravlenie_kursami\models\FizLico;

class Tema extends \app\records\Tema
{
    public function getPrepodavatel_fiz_lico_rel()
    {
        $query = parent::getPrepodavatel_fiz_lico_rel(); 
        $query->modelClass = FizLico::className();
        
        return $query;
    }
}