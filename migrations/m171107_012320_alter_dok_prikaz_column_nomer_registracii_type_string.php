<?php

use yii\db\Migration;

class m171107_012320_alter_dok_prikaz_column_nomer_registracii_type_string extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE dok_prikaz ALTER COLUMN nomer_registracii TYPE CHAR(10)');

    }

    public function down()
    {
        echo "m171107_012320_alter_dok_prikaz_column_nomer_registracii_type_string cannot be reverted.\n";

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
