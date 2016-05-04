<?php
namespace app\records;

use app\base\ActiveQuery;
use app\base\ActiveRecord;

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
class PodrazdelKursa extends ActiveRecord
{
    /**
     * @return ActiveQuery
     */
    public function getRazdel_rel()
    {
        return $this
            ->hasOne(RazdelKursa::className(), ['id' => 'razdel'])
            ->inverseOf('podrazdely_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getTemy_rel()
    {
        return $this
            ->hasMany(Tema::className(), ['podrazdel' => 'id'])
            ->inverseOf('podrazdel_rel');
    }
    
}