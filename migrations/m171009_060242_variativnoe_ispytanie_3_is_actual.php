<?php

use yii\db\Migration;

class m171009_060242_variativnoe_ispytanie_3_is_actual extends Migration
{
    public function up()
    {
        $this->addColumn('attestacionnoe_variativnoe_ispytanie_3', 'actual', \yii\db\pgsql\Schema::TYPE_BOOLEAN." DEFAULT TRUE");
    }

    public function down()
    {
        $this->dropColumn('attestacionnoe_variativnoe_ispytanie_3', 'actual');
    }
}
