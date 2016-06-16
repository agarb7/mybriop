<?php
namespace app\records;

use app\base\ActiveQuery;
use app\base\ActiveRecord;

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
 * @property integer $chasy
 * @property integer $nedelya
 * @property integer $prepodavatel_fiz_lico
 * @property boolean $prepodavatel_vakansiya
 * @property FizLico $prepodavatel_fiz_lico_rel
 */
class Tema extends ActiveRecord
{
    public static function tableName()
    {
        return 'tema';
    }

    /**
     * @return ActiveQuery
     */
    public function getPrepodavatel_fiz_lico_rel()
    {
        return $this->hasOne(
            FizLico::className(),
            ['id' => 'prepodavatel_fiz_lico']
        );
    }

    /**
     * @return ActiveQuery
     */
    public function getPodrazdel_rel()
    {
        return $this
            ->hasOne(PodrazdelKursa::className(), ['id' => 'podrazdel'])
            ->inverseOf('temy_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getZanyatiya_rel()
    {
        return $this
            ->hasMany(Zanyatie::className(), ['tema' => 'id'])
            ->inverseOf('tema_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getTip_raboty_rel()
    {
        return $this
            ->hasOne(RabotaPoTeme::className(), ['id' => 'tip_raboty']);
    }
}
