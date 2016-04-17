<?php

use yii\db\Migration;

class m160415_005115_rol_kadry extends Migration
{
    public function up()
    {
        $this->execute('alter type rol add value \'kadr_otd\' AFTER \'sot_att\'');
    }

    public function down()
    {
        echo "m160415_005115_rol_kadry cannot be reverted.\n";

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
