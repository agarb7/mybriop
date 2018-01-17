<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m180114_073345_add_column_nachalo_konec_rabotnik_attestacionnoj_komissii
 */
class m180114_073345_add_column_nachalo_konec_rabotnik_attestacionnoj_komissii extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('rabotnik_attestacionnoj_komissii', 'nachalo', Schema::TYPE_INTEGER);
        $this->addColumn('rabotnik_attestacionnoj_komissii', 'konec', Schema::TYPE_INTEGER);
        $this->addForeignKey(
            'rabotnik_attestacionnoj_komissii_vremya_nachalo_fkey',
            'rabotnik_attestacionnoj_komissii',
            'nachalo',
            'vremya_provedeniya_attestacii',
            'id');
        $this->addForeignKey(
            'rabotnik_attestacionnoj_komissii_vremya_konec_fkey',
            'rabotnik_attestacionnoj_komissii',
            'konec',
            'vremya_provedeniya_attestacii',
            'id');

        $sql=<<<SQL
UPDATE rabotnik_attestacionnoj_komissii SET nachalo = 1, konec = 27;
SQL;
        $this->execute($sql);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180114_073345_add_column_nachalo_konec_rabotnik_attestacionnoj_komissii cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180114_073345_add_column_nachalo_konec_rabotnik_attestacionnoj_komissii cannot be reverted.\n";

        return false;
    }
    */
}
