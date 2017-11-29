<?php

use yii\db\Migration;

/**
 * Class m171129_064220_add_column_rabochij_telefon__zayavlenie_na_attestaciyu
 */
class m171129_064220_add_column_rabochij_telefon__zayavlenie_na_attestaciyu extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('zayavlenie_na_attestaciyu','rabochij_telefon','telefonnyj_nomer DEFAULT NULL');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171129_064220_add_column_rabochij_telefon__zayavlenie_na_attestaciyu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171129_064220_add_column_rabochij_telefon__zayavlenie_na_attestaciyu cannot be reverted.\n";

        return false;
    }
    */
}
