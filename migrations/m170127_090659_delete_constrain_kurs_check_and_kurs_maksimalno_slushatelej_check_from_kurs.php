<?php

use yii\db\Migration;

class m170127_090659_delete_constrain_kurs_check_and_kurs_maksimalno_slushatelej_check_from_kurs extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE kurs DROP CONSTRAINT kurs_check RESTRICT;
SQL;
        $this->execute($sql);

        $sql = <<<SQL
ALTER TABLE kurs DROP CONSTRAINT kurs_maksimalno_slushatelej_check RESTRICT;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m170127_090659_delete_constrain_kurs_check_and_kurs_maksimalno_slushatelej_check_from_kurs cannot be reverted.\n";

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

