<?php

use yii\db\Schema;
use yii\db\Migration;

class m151129_122740_dolzhnosti_attestacionnoi_komisii extends Migration
{

    public function safeUp()
    {
        $this->createTable('dolzhnost_attestacionnoj_komissii',[
            'id' => Schema::TYPE_PK,
            'attestacionnaya_komissiya' => Schema::TYPE_INTEGER.' NOT NULL references attestacionnaya_komissiya(id)',
            'dolzhnost' => Schema::TYPE_INTEGER.' NOT NULL references dolzhnost(id)'
        ]);

        $this->renameColumn('rabotnik_attestacionnoj_komissii','attestatsionnaya_komissiya','attestacionnaya_komissiya');
    }

    public function safeDown()
    {
        echo "m151129_122740_dolzhnosti_attestacionnoi_komisii cannot be reverted.\n";

        return false;
    }

}
