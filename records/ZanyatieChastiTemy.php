<?php
namespace app\records;

use app\base\ActiveRecord;

/**
 * ZanyatieChastiTemy record
 *
 * @property integer $tema
 * @property integer $chast_temy
 * @property integer $zanyatie
 *
 * @property Zanyatie $zanyatie_rel
 */
class ZanyatieChastiTemy extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zanyatie_chasti_temy';
    }

    public function getTema_rel()
    {
        return $this->hasOne(Tema::className(), ['id' => 'tema']);
    }

    public function getZanyatie_rel()
    {
        return $this->hasOne(Zanyatie::className(), ['id' => 'zanyatie']);
    }
}