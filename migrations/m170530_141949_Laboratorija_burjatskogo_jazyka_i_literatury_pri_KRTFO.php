<?php

use yii\db\Migration;

class m170530_141949_Laboratorija_burjatskogo_jazyka_i_literatury_pri_KRTFO extends Migration
{
    public function up()
    {
        /**
         * кафедра развития технологии филологического образования id=16
         */
        $this->insert('dolzhnost_fiz_lica_na_rabote', [
            'rabota_fiz_lica' => 36,
            'strukturnoe_podrazdelenie' => 16,
            'dolzhnost' => 58,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => true,
        ]); // Цырендоржиева Баирма Дамбиевна
        $this->insert('dolzhnost_fiz_lica_na_rabote', [
            'rabota_fiz_lica' => 40,
            'strukturnoe_podrazdelenie' => 16,
            'dolzhnost' => 845,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => true,
        ]); // Цыденова Ханда Гунсоновна

        /**
         * кафедра инновационного проектирования id=2
         */
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
        ], 'id=37'); // Цырендоржиева Баирма Дамбиевна
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
        ], 'id=41'); // Цыденова Ханда Гунсоновна

        /**
         * лаборатория этнокультурного образования id=8
         */
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
        ], 'id=36'); // Цырендоржиева Баирма Дамбиевна
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
        ], 'id=40'); // Цыденова Ханда Гунсоновна
    }

    public function down()
    {
        echo "m170530_141949_Laboratorija_burjatskogo_jazyka_i_literatury_pri_KRTFO cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
