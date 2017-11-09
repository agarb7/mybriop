<?php

use yii\db\Migration;

class m171106_133834_updating_enum_org_tip_raboty extends Migration
{
    public function up()
    {
        $this->execute('ALTER TYPE org_tip_raboty RENAME TO org_tip_raboty_old');
        $this->execute('CREATE TYPE org_tip_raboty AS ENUM (\'osn\',\'sovm_vnut\',\'sovm_vnesh\')');
        $this->execute('ALTER TABLE rabota_fiz_lica ALTER COLUMN org_tip TYPE org_tip_raboty USING org_tip::text::org_tip_raboty');
        $this->execute('DROP TYPE org_tip_raboty_old');
    }

    public function down()
    {
        echo "m171106_133834_updating_enum_org_tip_raboty cannot be reverted.\n";

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
