<?php

use yii\db\Migration;

class m160111_094319_kurs_plan_prospekt_god extends Migration
{
    public function safeUp()
    {
        $this->execute('alter table kurs add column plan_prospekt_god date');
        $this->execute('update kurs set plan_prospekt_god = \'2015-01-01\'');
        $this->execute('alter table kurs alter column plan_prospekt_god set not null');
    }

    public function safeDown()
    {
        echo "m160111_094319_kurs_plan_prospekt_god cannot be reverted.\n";

        return false;
    }
}
