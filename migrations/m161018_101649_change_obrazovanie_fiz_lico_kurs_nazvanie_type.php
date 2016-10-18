<?php

use yii\db\Schema;
use yii\db\Migration;

class m161018_101649_change_obrazovanie_fiz_lico_kurs_nazvanie_type extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->alterColumn('obrazovanie_fiz_lica','kurs_nazvanie','varchar(2028)');
    }

    public function safeDown()
    {
        echo "m161018_101649_change_obrazovanie_fiz_lico_kurs_nazvanie_type cannot be reverted.\n";

        return false;
    }
}
