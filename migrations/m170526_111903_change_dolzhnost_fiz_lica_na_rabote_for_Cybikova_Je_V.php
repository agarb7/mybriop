<?php

use yii\db\Migration;

class m170526_111903_change_dolzhnost_fiz_lica_na_rabote_for_Cybikova_Je_V extends Migration
{
    public function up()
    {
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
        ], 'id=4725'); // Цыбикова Э.В.
    }

    public function down()
    {
        echo "m170526_111903_change_dolzhnost_fiz_lica_na_rabote_for_Cybikova_Je_V cannot be reverted.\n";

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
