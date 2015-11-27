<?php
namespace app\entities;

/**
 * RazdelKursa record
 *
 * @property integer $id
 * @property integer $kurs
 * @property integer $nomer
 * @property string $tip
 * @property integer $nazvanie
 */
class RazdelKursa extends EntityBase
{
    public function getKursRel()
    {
        return $this->hasOne(Kurs::className(), ['id' => 'kurs'])->inverseOf('razdelyKursaRel');
    }

    public function getPodrazdelyKursaRel()
    {
        return $this->hasMany(PodrazdelKursa::className(), ['razdel' => 'id'])->inverseOf('razdelKursaRel');
    }

    public function getNazvanieDlyaRazdelaKursaRel()
    {
        return $this->hasOne(NazvanieDlyaRazdelaKursa::className(), ['id' => 'nazvanie']);
    }
}