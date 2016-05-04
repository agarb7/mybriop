<?php
namespace app\upravlenie_kursami\raspisanie\models;

use yii\base\InvalidConfigException;
use yii\base\Model;

use app\records\Tema;

class ChastTemy extends Model
{
    /**
     * @var Tema
     */
    public $tema;

    /**
     * @var integer
     */
    public $chast;
    
    public function init()
    {
        if (!$this->tema instanceof Tema || !$this->chast)
            throw new InvalidConfigException;
    }
    
    public function getTema_nazvanie_chast()
    {
        $result = $this->tema->nazvanie;
        
        if (!$this->getIsUnique())
            $result .= " ($this->chast часть)";
        
        return $result;
    }
    
    public function getIsZanyatieExists()
    {
        return $this->tema
            ->getZanyatiya_rel()
            ->where(['chast_temy' => $this->chast])
            ->exists();
    }
    
    public function getIsUnique()
    {
        return $this->tema->chasy == 2;
    }
}