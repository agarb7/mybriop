<?php
namespace app\entities;

class KategoriyaSlushatelya extends EntityBase
{
    public function getKursyRel()
    {
        return $this
            ->hasMany(Kurs::className(), ['id' => 'kurs'])
            ->viaTable('kategoriya_slushatelya_kursa', ['kategoriya_slushatelya' => 'id']);
    }
}
