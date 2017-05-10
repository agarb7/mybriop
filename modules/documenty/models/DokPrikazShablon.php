<?php
namespace app\modules\documenty\models;

use yii\db\ActiveRecord;

class DokPrikazShablon extends ActiveRecord
{
    public function getShablonAtributy()
    {
        return $this->hasMany(DokPrikazShablonAtribut::className(), ['shablon_id' => 'id']);
    }

    public function getAtributyRel()
    {
        return $this->hasMany(DokSpisokAtributov::className(), ['id' => 'atribut_id'])->via('shablonAtributy');
    }
}