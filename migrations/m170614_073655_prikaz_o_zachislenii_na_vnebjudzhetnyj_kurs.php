<?php

use yii\db\Migration;

class m170614_073655_prikaz_o_zachislenii_na_vnebjudzhetnyj_kurs extends Migration
{
    public function up()
    {
        $this->insert('dok_prikaz_shablon', [
            'tip' => 'О зачислении на внебюджетные курсы',
            'shablon' => 'zachislenie2',
        ]);

        $this->insert('dok_spisok_atributov', [
            'nazvanie' => 'osnovanie',
            'nazvanie_tekst' => 'основание',
        ]);

        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 3,
            'atribut_id' => 1,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 3,
            'atribut_id' => 2,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 3,
            'atribut_id' => 3,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 3,
            'atribut_id' => 4,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 3,
            'atribut_id' => 5,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 3,
            'atribut_id' => 6,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 3,
            'atribut_id' => 7,
        ]);

        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 3,
            'porjadok' => 1,
            'dejstvie' => 'внесение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 3,
            'porjadok' => 2,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 3,
            'porjadok' => 3,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 3,
            'porjadok' => 4,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 3,
            'porjadok' => 5,
            'dejstvie' => 'утверждение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 3,
            'porjadok' => 6,
            'dejstvie' => 'регистрация',
        ]);

        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 1,
            'shablon_soglasovanie_id' => 13,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 2,
            'shablon_soglasovanie_id' => 14,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 3,
            'shablon_soglasovanie_id' => 15,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 4,
            'shablon_soglasovanie_id' => 16,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 5,
            'shablon_soglasovanie_id' => 17,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 6,
            'shablon_soglasovanie_id' => 18,
        ]);
    }

    public function down()
    {
        echo "m170614_073655_prikaz_o_zachislenii_na_vnebjudzhetnyj_kurs cannot be reverted.\n";

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
