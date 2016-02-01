<?php
namespace app\entities;

/**
 * Tema record
 *
 * @property integer $id
 * @property integer $podrazdel
 * @property integer $nomer
 * @property string $nazvanie
 * @property string $soderzhanie
 * @property integer $tip_raboty
 * @property integer $forma_kontrolya
 * @property string $chasy
 * @property integer $nedelya
 * @property integer $prepodavatel_fiz_lico
 * @property boolean $prepodavatel_vakansiya
 */
class Tema extends EntityBase
{
    public function getPodrazdelKursaRel()
    {
        return $this->hasOne(PodrazdelKursa::className(), ['id' => 'podrazdel'])->inverseOf('temyRel');
    }

    public function getKimRel()
    {
        return $this
            ->hasMany(Kim::className(), ['id' => 'kim'])
            ->viaTable('kim_temy', ['tema' => 'id'])
            ->from(Kim::tableName().' kim_tema');
    }

    public function getUmkRel()
    {
        return $this
            ->hasMany(Umk::className(), ['id' => 'umk'])
            ->viaTable('umk_temy', ['tema' => 'id'])
            ->from(Umk::tableName().' umk_tema');
    }

    public function getFormaKontrolyaVTechenieKursaRel()
    {
        return $this
            ->hasOne(FormaKontrolyaVTechenieKursa::className(), ['id' => 'forma_kontrolya']);
    }
}
