<?php

use yii\db\Migration;

class m170518_084649_fix_delete_tip_inaya_for_slushateli extends Migration
{
    public function up()
    {
$sql=<<<SQL
UPDATE dolzhnost SET tip = NULL
WHERE id IN (SELECT id FROM dolzhnost WHERE tip = 'inaya' AND obschij = 'f');
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m170518_084649_fix_delete_tip_inaya_for_slushateli cannot be reverted.\n";

        return false;
    }
}
