<?php

use yii\db\Migration;
use yii\db\Schema;

class m170423_025926_add_attest_kom_otsen_list extends Migration
{
    public function safeUp()
    {
        $this->createTable('att_komissii_otsenochnogo_lista', [
            'id' => Schema::TYPE_PK,
            'otsenochnyj_list_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'attestacionnaya_komissiya_id' => Schema::TYPE_INTEGER.' NOT NULL',
        ]);

        $this->addForeignKey(
            'att_komissii_otsenochnogo_lista_otsenochnyj_list_fkey',
            'att_komissii_otsenochnogo_lista',
            'otsenochnyj_list_id',
            'otsenochnyj_list',
            'id');

        $this->addForeignKey(
            'att_komissii_otsenochnogo_lista_attestacionnaya_komissiya_fkey',
            'att_komissii_otsenochnogo_lista',
            'attestacionnaya_komissiya_id',
            'attestacionnaya_komissiya',
            'id');
    }

    public function safeDown()
    {
        $this->dropForeignKey('att_komissii_otsenochnogo_lista_attestacionnaya_komissiya_fkey', 'att_komissii_otsenochnogo_lista');
        $this->dropForeignKey('att_komissii_otsenochnogo_lista_otsenochnyj_list_fkey', 'att_komissii_otsenochnogo_lista');
        $this->dropTable('att_komissii_otsenochnogo_lista');
    }
}
