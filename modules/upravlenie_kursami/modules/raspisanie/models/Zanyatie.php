<?php
namespace app\upravlenie_kursami\raspisanie\models;

use app\enums2\FormaZanyatiya;
use app\validators\Enum2Validator;
use yii\helpers\ArrayHelper;

class Zanyatie extends \app\records\Zanyatie
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['data', 'date', 'format' => 'yyyy-MM-dd'], // todo min, max and unique
            ['nomer', 'in', 'range' => range(1, Day::$zanyatiyaMax)], // todo range and unique
            ['tema', 'integer'], //todo exist and not already used
            ['chast_temy', 'integer'], //todo exist and not already used
            ['prepodavatel', 'integer'], //todo exist
            ['auditoriya', 'integer'], //todo exist
            ['forma', Enum2Validator::className(), 'enum' => FormaZanyatiya::className()]
        ];
    }

    public function getTema_nazvanie_chast()
    {
        $tema = $this->tema_rel;
        if ($tema === null || $this->chast_temy === null)
            return null;
        
        $chastTemy = new ChastTemy(['tema' => $tema, 'chast' => $this->chast_temy]); 
        
        return $chastTemy->tema_nazvanie_chast;        
    }
    
    public function getTema_tip_raboty_nazvanie()
    {
        return ArrayHelper::getValue($this, 'tema_rel.tip_raboty_rel.nazvanie');
    }

    /**
     * @param Kurs|null $kurs
     */
    public function setDefaultsFromKurs($kurs = null)        
    {
        if ($kurs === null)
            $kurs = $this->kurs_rel;
        
        $this->prepodavatel = ArrayHelper::getValue($this, 'tema_rel.prepodavatel_fiz_lico');
        $this->auditoriya = $kurs->auditoriya_po_umolchaniyu;
    }
}