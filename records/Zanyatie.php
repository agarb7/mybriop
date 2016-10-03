<?php
namespace app\records;

use app\base\ActiveQuery;
use app\base\ActiveRecord;

/**
 * Zanyatie record
 *
 * @property integer $id
 * @property string  $data
 * @property string  $forma
 * @property integer $nomer
 * @property integer $prepodavatel
 * @property integer $auditoriya
 * @property string $nazvanie
 */
class Zanyatie extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zanyatie';
    }

    /**
     * @return ActiveQuery
     */
    public function getPrepodavatel_rel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'prepodavatel']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAuditoriya_rel()
    {
        return $this->hasOne(Auditoriya::className(), ['id' => 'auditoriya']);
    }

    /**
     * @return ActiveQuery
     */
    public function getZanyatiya_chastej_tem_rel()
    {
        return $this->hasMany(ZanyatieChastiTemy::className(), ['zanyatie' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTemy_rel()
    {
        return $this
            ->hasMany(Tema::className(), ['id' => 'tema'])
            ->via('zanyatiya_chastej_tem_rel');
    }

}
