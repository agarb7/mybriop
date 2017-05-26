<?php

use yii\db\Migration;

class m170525_145130_change_dolzhnost_kle extends Migration
{
    public function up()
    {
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'strukturnoe_podrazdelenie' => 1,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
        ], 'id=42'); // Халудорова Л.Е.
    }

    public function down()
    {
        echo "m170525_145130_change_dolzhnost_kle cannot be reverted.\n";

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
