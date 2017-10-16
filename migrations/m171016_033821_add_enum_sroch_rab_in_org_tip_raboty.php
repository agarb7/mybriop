<?php

use yii\db\Migration;

class m171016_033821_add_enum_sroch_rab_in_org_tip_raboty extends Migration
{
    public function up()
    {
        $sql=<<<SQL
ALTER TYPE org_tip_raboty ADD VALUE 'sroch';
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m171016_033821_add_enum_sroch_rab_in_org_tip_raboty cannot be reverted.\n";

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
