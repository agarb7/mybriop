<?php
namespace app\records;

use app\base\ActiveQuery;
use app\base\ActiveRecord;

/**
 * Zanyatie record
 *
 * @property integer $id
 * @property integer $kurs
 * @property integer $tema
 * @property integer $chast_temy
 * @property string  $data
 * @property integer $nomer
 * @property integer $prepodavatel
 * @property integer $auditoriya
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
    public function getKurs_rel()
    {
        return $this
            ->hasOne(Kurs::className(), ['id' => 'kurs'])
            ->inverseOf('zanyatiya_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getTema_rel()
    {
        return $this
            ->hasOne(Tema::className(), ['id' => 'tema'])
            ->inverseOf('zanyatiya_rel');
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
}
