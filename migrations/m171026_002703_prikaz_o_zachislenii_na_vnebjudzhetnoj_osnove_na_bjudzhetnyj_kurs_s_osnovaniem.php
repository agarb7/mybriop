<?php

use yii\db\Migration;

class m171026_002703_prikaz_o_zachislenii_na_vnebjudzhetnoj_osnove_na_bjudzhetnyj_kurs_s_osnovaniem extends Migration
{
    public function up()
    {
        $this->insert('dok_prikaz_shablon', [
            'tip' => 'О зачислении на бюджетные курсы на внебюджетной основе c редакцией основания',
            'shablon' => 'zachislenie',
        ]);

        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 5,
            'atribut_id' => 1,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 5,
            'atribut_id' => 2,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 5,
            'atribut_id' => 3,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 5,
            'atribut_id' => 4,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 5,
            'atribut_id' => 5,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 5,
            'atribut_id' => 6,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 5,
            'atribut_id' => 7,
        ]);
        

        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 5,
            'porjadok' => 1,
            'dejstvie' => 'внесение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 5,
            'porjadok' => 2,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 5,
            'porjadok' => 3,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 5,
            'porjadok' => 4,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 5,
            'porjadok' => 5,
            'dejstvie' => 'утверждение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 5,
            'porjadok' => 6,
            'dejstvie' => 'регистрация',
        ]);

        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 1,
            'shablon_soglasovanie_id' => 25,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 2,
            'shablon_soglasovanie_id' => 26,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 3,
            'shablon_soglasovanie_id' => 27,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 4,
            'shablon_soglasovanie_id' => 28,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 5,
            'shablon_soglasovanie_id' => 29,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 6,
            'shablon_soglasovanie_id' => 30,
        ]);
    }

    public function down()
    {
        echo "m171026_002703_prikaz_o_zachislenii_na_vnebjudzhetnoj_osnove_na_bjudzhetnyj_kurs_s_osnovaniem cannot be reverted.\n";

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
