<?php

use yii\db\Schema;
use yii\db\Migration;

class m170130_142552_add_columns_pol_and_obshhij_stazh_and_uchenaja_stepen_into_fiz_lico extends Migration
{
    public function safeUp()
    {
        $this->createTable('uchenaja_stepen', [
            'id' => Schema::TYPE_PK,
            'nazvanie' => Schema::TYPE_STRING . ' NOT NULL',
        ]);
        $this->insert('uchenaja_stepen', [
            'nazvanie' => 'нет',]);
        $this->insert('uchenaja_stepen', [
            'nazvanie' => 'кандидат наук',]);
        $this->insert('uchenaja_stepen', [
            'nazvanie' => 'доктор наук']);

        $this->addColumn('fiz_lico', 'pol', $this->integer(1));
        $this->addColumn('fiz_lico', 'obshhij_stazh', 'stazh null');
        $this->addColumn('fiz_lico', 'uchenaja_stepen', $this->integer());
        $this->addForeignKey(
            'fiz_lico_uchenaja_stepen_fkey',
            'fiz_lico',
            'uchenaja_stepen',
            'uchenaja_stepen',
            'id',
            'CASCADE'
        );

        $this->addColumn('kurs_fiz_lica', 'ped_stazh', 'stazh null');
        $this->addColumn('kurs_fiz_lica', 'obshhij_stazh', 'stazh null');
        $this->addColumn('kurs_fiz_lica', 'uchenaja_stepen', $this->integer());
        $this->addForeignKey(
            'kurs_fiz_lica_uchenaja_stepen_fkey',
            'kurs_fiz_lica',
            'uchenaja_stepen',
            'uchenaja_stepen',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
       $this->dropForeignKey(
            'fiz_lico_uchenaja_stepen_fkey',
            'fiz_lico'
        );
        $this->dropColumn('fiz_lico', 'pol');
        $this->dropColumn('fiz_lico', 'obshhij_stazh');
        $this->dropColumn('fiz_lico', 'uchenaja_stepen');

        $this->dropForeignKey(
            'kurs_fiz_lica_uchenaja_stepen_fkey',
            'kurs_fiz_lica'
        );
        $this->dropColumn('kurs_fiz_lica', 'ped_stazh');
        $this->dropColumn('kurs_fiz_lica', 'obshhij_stazh');
        $this->dropColumn('kurs_fiz_lica', 'uchenaja_stepen');

        $this->dropTable('uchenaja_stepen');
    }
}
