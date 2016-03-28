<?php

use yii\db\Schema;
use yii\db\Migration;

class m160327_020648_add_to_roli_sotrudnik_attestacionnoj_komissii extends Migration
{
    public function up()
    {
        $this->execute('alter type rol add value \'sot_att\' AFTER \'ruk_att\'');
    }

    public function down()
    {

    }


//    // Use safeUp/safeDown to run migration code within a transaction
//    public function safeUp()
//    {
//
//    }
//
//    public function safeDown()
//    {
//    }

}
