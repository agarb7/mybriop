<?php

use yii\db\Schema;
use yii\db\Migration;

class m160402_034254_ostenochnyj_list_roditel_link extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160402_034254_ostenochnyj_list_roditel_link cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('struktura_otsenochnogo_lista_zayvaleniya','struktura_otsenochnogo_lista',Schema::TYPE_BIGINT.' not null');
        $this->addColumn('struktura_otsenochnogo_lista_zayvaleniya','roditel', Schema::TYPE_BIGINT.' null');
    }

    public function safeDown()
    {
        echo "m160402_034254_ostenochnyj_list_roditel_link cannot be reverted.\n";
        return false;
    }

}
