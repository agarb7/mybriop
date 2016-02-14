<?php

use yii\db\Schema;
use yii\db\Migration;

class m160206_122255_otraslevoe_soglashenie extends Migration
{
    public function up()
    {
        $this->createTable('otraslevoe_soglashenie',[
            'id' => Schema::TYPE_PK,
            'nazvanie' => 'squeezed_text NOT NULL'

        ]);
        //'zayavlenie_na_attestaciyu' => Schema::TYPE_INTEGER.' NOT NULL references zayavlenie_na_attestaciyu(id)',
    }

    public function down()
    {
        echo "m160206_122255_otraslevoe_soglashenie cannot be reverted.\n";

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
