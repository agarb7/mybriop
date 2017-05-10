<?php

use yii\db\Migration;

class m170429_164427_set_tip_dolzhnosti_uchprep extends Migration
{
    public function up()
    {
$sql=<<<SQL
ALTER TYPE tip_dolzhnosti ADD VALUE 'uchprep';
SQL;
        $this->execute($sql);
$sql=<<<SQL
UPDATE dolzhnost SET tip = 'uchprep'
WHERE obschij = TRUE AND lower(nazvanie) LIKE '%учитель%' AND tip ISNULL;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m170429_164427_set_tip_dolzhnosti_uchprep cannot be reverted.\n";

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
