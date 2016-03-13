<?php

use yii\db\Schema;
use yii\db\Migration;

class m160313_113135_zayavlenie_na_attestaciyu_dobavlenie_kolonok extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160313_113135_zayavlenie_na_attestaciyu_dobavlenie_kolonok cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('zayavlenie_na_attestaciyu','domashnij_telefon','telefonnyj_nomer');
        $this->addColumn('zayavlenie_na_attestaciyu','prilozhenie1','squeezed_text');
        $this->addColumn('zayavlenie_na_attestaciyu','provesti_zasedanie_bez_prisutstviya',Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE');
    }

    public function safeDown()
    {
        echo "m160313_113135_zayavlenie_na_attestaciyu_dobavlenie_kolonok cannot be reverted.\n";

        return false;
    }

}
