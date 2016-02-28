<?php
namespace app\records;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * RazdelKursa record
 * @property int id
 * @property int kurs
 * @property int nomer
 * @property string tip
 * @property string nazvanie
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
}