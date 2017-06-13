<?php

use yii\db\Migration;

class m170609_073635_is_actual_for_organizaciya_and_strukturnoe_podrazdelenie extends Migration
{
    public function up()
    {
        $this->addColumn('organizaciya', 'actual', \yii\db\pgsql\Schema::TYPE_BOOLEAN." DEFAULT TRUE");
        $this->addColumn('strukturnoe_podrazdelenie', 'actual', \yii\db\pgsql\Schema::TYPE_BOOLEAN." DEFAULT TRUE");
    }

    public function down()
    {
        $this->dropColumn(organizaciya, 'actual');
        $this->dropColumn(strukturnoe_podrazdelenie, 'actual');
    }
}
