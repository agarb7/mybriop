<?php

use yii\db\Migration;

class m170421_031647_add_data_for_sotrudniki_briop extends Migration
{
    public function safeUp()
    {
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'strukturnoe_podrazdelenie' => 18,
            'rukovoditel_strukturnogo_podrazdeleniya' => true,
        ], 'id=4725'); // Цыбикова Э.В.
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'strukturnoe_podrazdelenie' => 18,
            'rukovoditel_strukturnogo_podrazdeleniya' => true,
        ], 'id=82'); // Фомицкая Г.Н.
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'dolzhnost' => 47,
            'rukovoditel_strukturnogo_podrazdeleniya' => true,
        ], 'id=13'); // Тармаева Е.Р.
    }

    public function safeDown()
    {
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'strukturnoe_podrazdelenie' => null,
            'rukovoditel_strukturnogo_podrazdeleniya' => null,
        ], 'id=4725'); // Цыбикова Э.В.
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'strukturnoe_podrazdelenie' => null,
            'rukovoditel_strukturnogo_podrazdeleniya' => null,
        ], 'id=82'); // Фомицкая Г.Н.
    }
}
