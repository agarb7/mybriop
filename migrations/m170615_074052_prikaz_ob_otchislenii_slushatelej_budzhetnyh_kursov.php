<?php

use yii\db\Migration;

class m170615_074052_prikaz_ob_otchislenii_slushatelej_budzhetnyh_kursov extends Migration
{
    public function up()
    {
        $this->insert('dok_prikaz_shablon', [
            'tip' => 'Об отчислении слушателей курсов',
            'shablon' => 'prikaz-form',
        ]);

        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 4,
            'atribut_id' => 1,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 4,
            'atribut_id' => 2,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 4,
            'atribut_id' => 3,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 4,
            'atribut_id' => 4,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 4,
            'atribut_id' => 5,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 4,
            'atribut_id' => 6,
        ]);

        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 4,
            'porjadok' => 1,
            'dejstvie' => 'внесение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 4,
            'porjadok' => 2,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 4,
            'porjadok' => 3,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 4,
            'porjadok' => 4,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 4,
            'porjadok' => 5,
            'dejstvie' => 'утверждение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 4,
            'porjadok' => 6,
            'dejstvie' => 'регистрация',
        ]);

        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 1,
            'shablon_soglasovanie_id' => 19,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 2,
            'shablon_soglasovanie_id' => 20,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 3,
            'shablon_soglasovanie_id' => 21,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 4,
            'shablon_soglasovanie_id' => 22,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 5,
            'shablon_soglasovanie_id' => 23,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 6,
            'shablon_soglasovanie_id' => 24,
        ]);
    }

    public function down()
    {
        echo "m170615_074052_prikaz_ob_otchislenii_slushatelej_budzhetnyh_kursov cannot be reverted.\n";

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
