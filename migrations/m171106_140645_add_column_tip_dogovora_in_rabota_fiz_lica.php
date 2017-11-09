<?php

use yii\db\Migration;

class m171106_140645_add_column_tip_dogovora_in_rabota_fiz_lica extends Migration
{
    public function up()
    {
        $this->addColumn('rabota_fiz_lica','tip_dogovora','tip_dogovora_raboty DEFAULT NULL');
    }

    public function down()
    {
        echo "m171106_140645_add_column_tip_dogovora_in_rabota_fiz_lica cannot be reverted.\n";

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
