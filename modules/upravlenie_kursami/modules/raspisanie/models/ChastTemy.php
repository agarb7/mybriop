<?php
namespace app\upravlenie_kursami\raspisanie\models;


use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\Query;

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

    public function getIsInPotok()
    {
        return (new Query)
            ->from('zanyatie_chasti_temy zct')
            ->leftJoin('zanyatie_chasti_temy other', 'other.zanyatie = zct.zanyatie')
            ->where([
                'zct.tema' => $this->tema->id,
                'zct.chast_temy' => $this->chast
            ])
            ->count()>1;
    }
    
    public function getIsUnique()
    {
        return $this->tema->chasy == 2;
    }
}