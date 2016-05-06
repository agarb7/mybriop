<?php
namespace app\records;

use app\base\ActiveQuery;
use app\base\ActiveRecord;

/**
 * RazdelKursa record
 * 
 * @property integer $id
 * @property integer $kurs
 * @property integer $nomer
 * @property string $tip
 * @property integer $nazvanie
 * @property Kurs $kurs_rel
 * @property PodrazdelKursa[] $podrazdely_rel
 * @property NazvanieDlyaRazdelaKursa[] $nazvanie_rel
 */
class RazdelKursa extends ActiveRecord
{
    /**
     * @return ActiveQuery
     */
    public function getKurs_rel()
    {
        return $this
            ->hasOne(Kurs::className(), ['id' => 'kurs'])
            ->inverseOf('razdely_kursa_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getPodrazdely_rel()
    {
        return $this
            ->hasMany(PodrazdelKursa::className(), ['razdel' => 'id'])
            ->inverseOf('razdel_rel');
    }

    public function getNazvanie_rel()
    {
        return $this
            ->hasOne(NazvanieDlyaRazdelaKursa::className(), ['id' => 'nazvanie']);
    }
}