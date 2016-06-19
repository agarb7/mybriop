<?php

use yii\db\Schema;
use yii\db\Migration;

class m160619_031907_diplom_o_prof_perepodogtovke extends Migration
{
    public function up()
    {
        $this->execute('ALTER TYPE tip_dokumenta_ob_obrazovanii ADD VALUE \'dip_pp\' AFTER \'udost\'');
    }

    public function down()
    {
        echo "m160619_031907_diplom_o_prof_perepodogtovke cannot be reverted.\n";

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
