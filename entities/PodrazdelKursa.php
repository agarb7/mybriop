<?php
namespace app\entities;

/**
 * PodrazdelKursa record
 *
 * @property integer $id
 * @property integer $razdel
 * @property integer $nomer
 * @property string $nazvanie
 * @property integer $raschitano_chasov_lekcyj
 * @property integer $raschitano_chasov_praktik
 * @property integer $raschitano_chasov_srs
 * @property integer $forma_kontrolya
 * @property integer $chasy_kontrolya
 * @property integer $rukovoditel
 * @property string $aktualnost
 * @property string $cel
 * @property string $zadachi
 * @property string $planiruemye_rezultaty
 * @property string $mesto_discipliny_v_strukture_programmy
 * @property string $informacionnye_usloviya
 * @property string $uchebnometodicheskie_usloviya
 * @property string $kadrovye_usloviya
 * @property string $materialnotehnicheskie_usloviya
 * @property string $literatura
 * @property integer $status
 * @property integer $nedelya_nachalo
 * @property integer $nedelya_konec
 * @property boolean $rukovoditel_vakansiya
*/
class PodrazdelKursa extends EntityBase
{
    public function getRazdelKursaRel()
    {
        return $this->hasOne(RazdelKursa::className(), ['id' => 'razdel'])->inverseOf('podrazdelyKursaRel');
    }

    public function getTemyRel()
    {
        return $this->hasMany(Tema::className(), ['podrazdel' => 'id'])->inverseOf('podrazdelKursaRel');
    }

    public function getKimRel()
    {
        return $this
            ->hasMany(Kim::className(), ['id' => 'kim'])
            ->viaTable('kim_podrazdela_kursa', ['podrazdel_kursa' => 'id']);
    }

    public function getUmkRel()
    {
        return $this
            ->hasMany(Umk::className(), ['id' => 'umk'])
            ->viaTable('kim_podrazdela_kursa', ['podrazdel_kursa' => 'id']);
    }

    public function getFormaKontrolyaVTechenieKursaRel()
    {
        return $this
            ->hasOne(FormaKontrolyaVTechenieKursa::className(), ['id' => 'forma_kontrolya']);
    }
}