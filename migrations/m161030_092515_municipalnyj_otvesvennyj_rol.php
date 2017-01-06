<?php

use yii\db\Schema;
use yii\db\Migration;

class m161030_092515_municipalnyj_otvesvennyj_rol extends Migration
{
    public function up()
    {
        $this->execute('alter type rol add value \'mun_otv\' AFTER \'kadr_otd\'');
    }

    public function down()
    {
        echo "m161030_092515_municipalnyj_otvesvennyj_rol cannot be reverted.\n";

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
