<?php

use yii\db\Migration;

class m170613_074713_prikaz_o_zachislenii_na_vnebjudzhetnoj_osnove_na_bjudzhetnyj_kurs extends Migration
{
    public function up()
    {
        $this->update('dok_prikaz_shablon', ['tip' => 'О зачислении на бюджетные курсы'], ['id' => 1]);

        $this->insert('dok_prikaz_shablon', [
            'tip' => 'О зачислении на бюджетные курсы на внебюджетной основе',
            'shablon' => 'zachislenie',
        ]);

        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 2,
            'atribut_id' => 1,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 2,
            'atribut_id' => 2,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 2,
            'atribut_id' => 3,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 2,
            'atribut_id' => 4,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 2,
            'atribut_id' => 5,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 2,
            'atribut_id' => 6,
        ]);

        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 2,
            'porjadok' => 1,
            'dejstvie' => 'внесение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 2,
            'porjadok' => 2,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 2,
            'porjadok' => 3,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 2,
            'porjadok' => 4,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 2,
            'porjadok' => 5,
            'dejstvie' => 'утверждение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 2,
            'porjadok' => 6,
            'dejstvie' => 'регистрация',
        ]);

        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 1,
            'shablon_soglasovanie_id' => 7,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 2,
            'shablon_soglasovanie_id' => 8,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 3,
            'shablon_soglasovanie_id' => 9,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 4,
            'shablon_soglasovanie_id' => 10,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 5,
            'shablon_soglasovanie_id' => 11,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 6,
            'shablon_soglasovanie_id' => 12,
        ]);
    }

    public function down()
    {
        echo "m170613_074713_prikaz_o_zachislenii_na_vnebjudzhetnoj_osnove_na_bjudzhetnyj_kurs cannot be reverted.\n";

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
