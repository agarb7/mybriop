<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m180114_084042_add_column_nachalo_konec_attestacionnaya_komissiya
 */
class m180114_084042_add_column_nachalo_konec_attestacionnaya_komissiya extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('attestacionnaya_komissiya', 'nachalo', Schema::TYPE_INTEGER);
        $this->addColumn('attestacionnaya_komissiya', 'konec', Schema::TYPE_INTEGER);
        $this->addForeignKey(
            'attestacionnaya_komissiya_vremya_nachalo_fkey',
            'attestacionnaya_komissiya',
            'nachalo',
            'vremya_provedeniya_attestacii',
            'id');
        $this->addForeignKey(
            'attestacionnaya_komissiya_vremya_konec_fkey',
            'attestacionnaya_komissiya',
            'konec',
            'vremya_provedeniya_attestacii',
            'id');

        $sql=<<<SQL
UPDATE attestacionnaya_komissiya SET nachalo = 1, konec = 27;
SQL;
        $this->execute($sql);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180114_084042_add_column_nachalo_konec_attestacionnaya_komissiya cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180114_084042_add_column_nachalo_konec_attestacionnaya_komissiya cannot be reverted.\n";

        return false;
    }
    */
}
