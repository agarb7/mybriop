<?php

use yii\db\Schema;
use yii\db\Migration;

class m160223_053651_kurs_iup extends Migration
{
    public function safeUp()
    {
        $this->execute('alter table kurs add column iup boolean default false not null');
    }

    public function safeDown()
    {
        echo "m160223_053651_kurs_iup cannot be reverted.\n";

        return false;
    }
}
