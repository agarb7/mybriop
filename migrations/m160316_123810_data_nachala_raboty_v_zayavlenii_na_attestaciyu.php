<?php

use yii\db\Schema;
use yii\db\Migration;

class m160316_123810_data_nachala_raboty_v_zayavlenii_na_attestaciyu extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160316_123810_data_nachala_raboty_v_zayavlenii_na_attestaciyu cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('zayavlenie_na_attestaciyu','rabota_data_naznacheniya', Schema::TYPE_DATE.' NULL');
        $this->addColumn('zayavlenie_na_attestaciyu','rabota_data_naznacheniya_v_uchrezhdenii', Schema::TYPE_DATE.' NULL');
    }

    public function safeDown()
    {
    }

}
