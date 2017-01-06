<?php

use yii\db\Migration;

class m161204_064310_add_columns_to_kurs_for_raspisanie extends Migration
{
    public function up()
    {
        $this->addColumn('kurs', 'data_otpravki_v_uo', 'datetime null');
    }

    public function down()
    {
        echo "m161204_064310_add_columns_to_kurs_for_raspisanie cannot be reverted.\n";

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
