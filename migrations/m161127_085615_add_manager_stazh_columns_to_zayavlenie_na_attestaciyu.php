<?php

use yii\db\Migration;

class m161127_085615_add_manager_stazh_columns_to_zayavlenie_na_attestaciyu extends Migration
{
    public function up()
    {
        $this->addColumn('zayavlenie_na_attestaciyu', 'stazh_rukovodyashej_raboty','int NULL');
        $this->addColumn('zayavlenie_na_attestaciyu', 'stazh_obshij_trudovoj','int NULL');
    }

    public function down()
    {
        echo "m161127_085615_add_manager_stazh_columns_to_zayavlenie_na_attestaciyu cannot be reverted.\n";

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
