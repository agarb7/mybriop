<?php

use yii\db\Schema;
use yii\db\Migration;

class m161030_084216_municipalnyj_otvestvennyj_table extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m161030_084216_municipalnyj_otvestvennyj_table cannot be reverted.\n";
//
//        return false;
//    }

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->createTable('municipalnyj_otvestvennyj',[
            'id' => Schema::TYPE_PK,
            'district_id' => Schema::TYPE_BIGINT.' NOT NULL references adresnyj_objekt(id)',
            'fiz_lico' => Schema::TYPE_BIGINT.' NOT NULL references fiz_lico(id)'
        ]);
    }

    public function safeDown()
    {
        echo "m161030_084216_municipalnyj_otvestvennyj_table cannot be reverted.\n";

        return false;
    }
}
