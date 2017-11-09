<?php

use yii\db\Migration;

class m171106_135927_create_enum_tip_dogovora extends Migration
{
    public function up()
    {
        $this->execute('CREATE TYPE tip_dogovora_raboty AS ENUM (\'trud\',\'gph\')');
    }

    public function down()
    {
        echo "m171106_135927_create_enum_tip_dogovora cannot be reverted.\n";

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
