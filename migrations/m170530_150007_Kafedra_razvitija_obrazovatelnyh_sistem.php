<?php

use yii\db\Migration;

class m170530_150007_Kafedra_razvitija_obrazovatelnyh_sistem extends Migration
{
    public function up()
    {
        /**
         * кафедра развития образовательных систем id=3
         */
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'strukturnoe_podrazdelenie' => 3,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => true,
        ], 'id=10250'); // Содномов Сономбал Цыденович
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'strukturnoe_podrazdelenie' => 3,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => true,
            'dolzhnost' => 845,
        ], 'id=10251'); // Гармаева Татьяна Владимировна
    }

    public function down()
    {
        echo "m170530_150007_Kafedra_razvitija_obrazovatelnyh_sistem cannot be reverted.\n";

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
