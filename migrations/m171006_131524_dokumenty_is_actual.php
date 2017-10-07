<?php

use yii\db\Migration;

class m171006_131524_dokumenty_is_actual extends Migration
{
    public function up()
    {
        $this->addColumn('dok', 'actual', \yii\db\pgsql\Schema::TYPE_BOOLEAN." DEFAULT TRUE");
    }

    public function down()
    {
        $this->dropColumn('dok', 'actual');
    }
}
