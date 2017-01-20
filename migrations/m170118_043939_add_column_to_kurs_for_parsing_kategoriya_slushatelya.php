<?php

use yii\db\Migration;

class m170118_043939_add_column_to_kurs_for_parsing_kategoriya_slushatelya extends Migration
{
    public function up()
    {
        $this->addColumn('kurs', 'kategoriya_slushatelya', 'squeezed_text null');
    }

    public function down()
    {
        echo "m170118_043939_add_column_to_kurs_for_parsing_kategoriya_slushatelya cannot be reverted.\n";

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
