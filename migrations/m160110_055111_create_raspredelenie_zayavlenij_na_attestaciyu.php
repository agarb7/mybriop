<?php

use yii\db\Schema;
use yii\db\Migration;

class m160110_055111_create_raspredelenie_zayavlenij_na_attestaciyu extends Migration
{
    public function up()
    {
        $this->createTable('raspredelenie_zayavlenij_na_attestaciyu',[
            'id' => Schema::TYPE_PK,
            'rabotnik_attestacionnoj_komissii' => Schema::TYPE_INTEGER.' NOT NULL references rabotnik_attestacionnoj_komissii(id)',
            'zayavlenie_na_attestaciyu' => Schema::TYPE_INTEGER.' NOT NULL references zayavlenie_na_attestaciyu(id)'
        ]);
    }

    public function down()
    {
        echo "m160110_055111_create_raspredelenie_zayavlenij_na_attestaciyu cannot be reverted.\n";

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
