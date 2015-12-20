<?php

use yii\db\Schema;
use yii\db\Migration;

class m151220_050857_change_roli_enum extends Migration
{
    public function up()
    {
        $this->execute('alter type rol add value \'ruk_att\' AFTER \'att_otd\'');
    }

    public function down()
    {
        echo "m151220_050857_change_roli_enum cannot be reverted.\n";

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
