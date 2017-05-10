<?php

use yii\db\Migration;
use yii\db\Schema;

class m170212_141848_add_table_dok_prikazy extends Migration
{
    public function safeUp()
    {
        $this->createTable('dok_spisok_atributov', [
            'id' => Schema::TYPE_PK,
            'nazvanie' => 'nazvanie NOT NULL',
            'nazvanie_tekst' => 'nazvanie NOT NULL',
            'imja_tablicy' => 'nazvanie',
            'imja_polja' => 'nazvanie',
        ]);

        $this->createTable('dok_prikaz', [
            'id' => Schema::TYPE_PK,
            'nomer_registracii' => Schema::TYPE_INTEGER,
            'data_registracii' => Schema::TYPE_DATE,
            'status_podpisan'=> Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 0',
            'shablon_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'avtor_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'data_sozdanija' => Schema::TYPE_DATE.' NOT NULL',
            'redaktiruetsja' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 1',
        ]);

        $this->createTable('dok_prikaz_shablon', [
            'id' => Schema::TYPE_PK,
            'tip' => 'nazvanie NOT NULL',
            'shablon' => 'nazvanie NOT NULL',
        ]);
        $this->createTable('dok_prikaz_shablon_atribut', [
            'shablon_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'atribut_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'id_znachenija' => Schema::TYPE_INTEGER,
            'objazjatelnyj' => Schema::TYPE_SMALLINT.' NOT NULL DEFAULT 1',
        ]);
        $this->createTable('dok_prikaz_shablon_soglasovanie', [
            'id' => Schema::TYPE_PK,
            'shablon_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'porjadok' => Schema::TYPE_INTEGER.' NOT NULL',
            'dejstvie' => 'slovo NOT NULL',
        ]);
        $this->createTable('dok_roli', [
            'id' => Schema::TYPE_PK,
            'dolzhnost_id' => Schema::TYPE_INTEGER,
            'strukturnoe_podrazdelenie_id' => Schema::TYPE_INTEGER,
            'polzovatel_roli' => 'roli',
            'opisanie' => 'squeezed_text',
        ]);
        $this->createTable('dok_prikaz_shablon_ispolnitel', [
            'id' => Schema::TYPE_PK,
            'shablon_soglasovanie_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'roli_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'delegirovan_ot_id' => Schema::TYPE_INTEGER.' NULL',
            'delegirovan_data_nachalo' => Schema::TYPE_DATE.' NULL',
            'delegirovan_data_konec' => Schema::TYPE_DATE.' NULL',
        ]);

        $this->createTable('dok_prikaz_atribut', [
            'id' => Schema::TYPE_PK,
            'prikaz_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'atribut_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'znachenie' => Schema::TYPE_STRING,
            'id_znachenija' => Schema::TYPE_INTEGER,
        ]);
        $this->createTable('dok_prikaz_tablica', [
            'id' => Schema::TYPE_PK,
            'prikaz_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'kurs_fiz_lica_id' => Schema::TYPE_INTEGER,
            'fiz_lico_id' => Schema::TYPE_INTEGER,
        ]);

        $this->createTable('dok_process', [
            'id' => Schema::TYPE_PK,
            'porjadok' => Schema::TYPE_INTEGER.' NOT NULL',
            'nazvanie' => 'squeezed_text',
            'dok_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'roli_id' => Schema::TYPE_INTEGER.' NOT NULL',
            'komentarij' => 'squeezed_text',
            'sozdal_fiz_lico_id' => Schema::TYPE_INTEGER,
            'data_vnesenija' => Schema::TYPE_DATE,
            'ispolnil_fiz_lico_id' => Schema::TYPE_INTEGER,
            'data_zavershenija' => Schema::TYPE_DATE,
            'vernut_avtoru' => Schema::TYPE_BOOLEAN.' DEFAULT FALSE',
        ]);

        $this->createTable('dok', [
            'id' => Schema::TYPE_PK,
            'prikaz_id' => Schema::TYPE_INTEGER,
        ]);

        $this->addForeignKey(
            'dok_dok_prikaz_fkey',
            'dok',
            'prikaz_id',
            'dok_prikaz',
            'id');
        $this->addForeignKey(
            'dok_prikaz_dok_prikaz_shablon_fkey',
            'dok_prikaz',
            'shablon_id',
            'dok_prikaz_shablon',
            'id');
        $this->addForeignKey(
            'dok_prikaz_polzovatel_fkey',
            'dok_prikaz',
            'avtor_id',
            'polzovatel',
            'id');
        $this->addForeignKey(
            'dok_prikaz_shablon_atribut_dok_prikaz_shablon_fkey',
            'dok_prikaz_shablon_atribut',
            'shablon_id',
            'dok_prikaz_shablon',
            'id');
        $this->addForeignKey(
            'dok_prikaz_shablon_atribut_dok_spisok_atributov_fkey',
            'dok_prikaz_shablon_atribut',
            'atribut_id',
            'dok_spisok_atributov',
            'id');
        $this->addForeignKey(
            'dok_prikaz_shablon_soglasovanie_dok_prikaz_shablon_fkey',
            'dok_prikaz_shablon_soglasovanie',
            'shablon_id',
            'dok_prikaz_shablon',
            'id');

        $this->addForeignKey(
            'dok_roli_dolzhnost_fkey',
            'dok_roli',
            'dolzhnost_id',
            'dolzhnost',
            'id');
        $this->addForeignKey(
            'dok_roli_strukturnoe_podrazdelenie_fkey',
            'dok_roli',
            'strukturnoe_podrazdelenie_id',
            'strukturnoe_podrazdelenie',
            'id');

        $this->addForeignKey(
            'dok_prikaz_shablon_ispolnitel_dok_prikaz_shablon_soglasovanie_fkey',
            'dok_prikaz_shablon_ispolnitel',
            'shablon_soglasovanie_id',
            'dok_prikaz_shablon_soglasovanie',
            'id');
        $this->addForeignKey(
            'dok_prikaz_shablon_ispolnitel_dok_roli_fkey',
            'dok_prikaz_shablon_ispolnitel',
            'roli_id',
            'dok_roli',
            'id');
        $this->addForeignKey(
            'dok_prikaz_shablon_ispolnitel_fkey',
            'dok_prikaz_shablon_ispolnitel',
            'delegirovan_ot_id',
            'dok_prikaz_shablon_ispolnitel',
            'id');

        $this->addForeignKey(
            'dok_prikaz_atribut_dok_prikaz_fkey',
            'dok_prikaz_atribut',
            'prikaz_id',
            'dok_prikaz',
            'id');
        $this->addForeignKey(
            'dok_prikaz_atribut_dok_spisok_atributov_fkey',
            'dok_prikaz_atribut',
            'atribut_id',
            'dok_spisok_atributov',
            'id');
        $this->addForeignKey(
            'dok_prikaz_tablica_dok_prikaz_fkey',
            'dok_prikaz_tablica',
            'prikaz_id',
            'dok_prikaz',
            'id');
        $this->addForeignKey(
            'dok_prikaz_tablica_kurs_fiz_lica_fkey',
            'dok_prikaz_tablica',
            'kurs_fiz_lica_id',
            'kurs_fiz_lica',
            'id');
        $this->addForeignKey(
            'dok_prikaz_tablica_fiz_lico_fkey',
            'dok_prikaz_tablica',
            'fiz_lico_id',
            'fiz_lico',
            'id');
        $this->addForeignKey(
            'dok_process_dok_fkey',
            'dok_process',
            'dok_id',
            'dok',
            'id');
        $this->addForeignKey(
            'dok_process_dok_roli_fkey',
            'dok_process',
            'roli_id',
            'dok_roli',
            'id');
        $this->addForeignKey(
            'dok_process_sozdal_fiz_lico_fkey',
            'dok_process',
            'sozdal_fiz_lico_id',
            'fiz_lico',
            'id');
        $this->addForeignKey(
            'dok_process_ispolnil_fiz_lico_fkey',
            'dok_process',
            'ispolnil_fiz_lico_id',
            'fiz_lico',
            'id');


        $this->insert('dok_prikaz_shablon', [
            'tip' => 'О зачислении и назначении комиссии',
            'shablon' => 'zachislenie',
        ]);
        $this->insert('dok_spisok_atributov', [
            'nazvanie' => 'plan_prospekt',
            'nazvanie_tekst' => 'план-проспект',
        ]);
        $this->insert('dok_spisok_atributov', [
            'nazvanie' => 'programma',
            'nazvanie_tekst' => 'программа',
            'imja_tablicy' => 'kurs',
            'imja_polja' => 'nazvanie',
        ]);
        $this->insert('dok_spisok_atributov', [
            'nazvanie' => 'kategorija',
            'nazvanie_tekst' => 'категория слушателей',
        ]);
        $this->insert('dok_spisok_atributov', [
            'nazvanie' => 'objem_chasov',
            'nazvanie_tekst' => 'объем часов',
            'imja_tablicy' => 'kurs',
            'imja_polja' => 'raschitano_chasov',
        ]);
        $this->insert('dok_spisok_atributov', [
            'nazvanie' => 'nachalo',
            'nazvanie_tekst' => 'начало занятий',
        ]);
        $this->insert('dok_spisok_atributov', [
            'nazvanie' => 'konec',
            'nazvanie_tekst' => 'конец занятий',
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
        'shablon_id' => 1,
        'atribut_id' => 1,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 1,
            'atribut_id' => 2,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 1,
            'atribut_id' => 3,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 1,
            'atribut_id' => 4,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 1,
            'atribut_id' => 5,
        ]);
        $this->insert('dok_prikaz_shablon_atribut', [
            'shablon_id' => 1,
            'atribut_id' => 6,
        ]);

        $this->update('dolzhnost_fiz_lica_na_rabote',['strukturnoe_podrazdelenie'=>7, 'rukovoditel_strukturnogo_podrazdeleniya'=>true, 'dolzhnost'=>44],'id=20');

        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 1,
            'porjadok' => 1,
            'dejstvie' => 'внесение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 1,
            'porjadok' => 2,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 1,
            'porjadok' => 3,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 1,
            'porjadok' => 4,
            'dejstvie' => 'согласование',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 1,
            'porjadok' => 5,
            'dejstvie' => 'утверждение',
        ]);
        $this->insert('dok_prikaz_shablon_soglasovanie', [
            'shablon_id' => 1,
            'porjadok' => 6,
            'dejstvie' => 'регистрация',
        ]);
        
        $this->insert('dok_roli', [
            'polzovatel_roli' => '{ruk_kurs}',
            'opisanie' => 'внесение проекта приказа (руководитель курсов)',
        ]);
        $this->insert('dok_roli', [
            'dolzhnost_id' => 47,
            'opisanie' => 'согласование (руководитель кафедры, лаборатории, центра и т.п.)',
        ]);
        $this->insert('dok_roli', [
            'dolzhnost_id' => 44,
            'strukturnoe_podrazdelenie_id' => 7,
            'opisanie' => 'согласование (начальник учебного отдела)',
        ]);
        $this->insert('dok_roli', [
            'dolzhnost_id' => 50,
            'opisanie' => 'согласование (проректор по учебной работе)',
        ]);
        $this->insert('dok_roli', [
            'dolzhnost_id' => 41,
            'opisanie' => 'Утверждение',
        ]);
        $this->insert('dok_roli', [
            'dolzhnost_id' => 63,
            'strukturnoe_podrazdelenie_id' => 7,
            'opisanie' => 'Регистрация',
        ]);

        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 1,
            'shablon_soglasovanie_id' => 1,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 2,
            'shablon_soglasovanie_id' => 2,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 3,
            'shablon_soglasovanie_id' => 3,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 4,
            'shablon_soglasovanie_id' => 4,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 5,
            'shablon_soglasovanie_id' => 5,
        ]);
        $this->insert('dok_prikaz_shablon_ispolnitel', [
            'roli_id' => 6,
            'shablon_soglasovanie_id' => 6,
        ]);
    }

    public function safeDown()
    {
        $this->dropForeignKey('dok_prikaz_tablica_fiz_lico_fkey','dok_prikaz_tablica');
        $this->dropForeignKey('dok_prikaz_tablica_kurs_fiz_lica_fkey','dok_prikaz_tablica');
        $this->dropForeignKey('dok_prikaz_tablica_dok_prikaz_fkey', 'dok_prikaz_tablica');
        $this->dropForeignKey('dok_prikaz_atribut_dok_spisok_atributov_fkey', 'dok_prikaz_atribut');
        $this->dropForeignKey('dok_prikaz_atribut_dok_prikaz_fkey', 'dok_prikaz_atribut');
        $this->dropForeignKey('dok_prikaz_shablon_ispolnitel_fkey', 'dok_prikaz_shablon_ispolnitel');
        $this->dropForeignKey('dok_prikaz_shablon_ispolnitel_dok_roli_fkey', 'dok_prikaz_shablon_ispolnitel');
        $this->dropForeignKey('dok_prikaz_shablon_ispolnitel_dok_prikaz_shablon_soglasovanie_fkey', 'dok_prikaz_shablon_ispolnitel');
        $this->dropForeignKey('dok_prikaz_shablon_soglasovanie_dok_prikaz_shablon_fkey', 'dok_prikaz_shablon_soglasovanie');
        $this->dropForeignKey('dok_prikaz_shablon_atribut_dok_spisok_atributov_fkey', 'dok_prikaz_shablon_atribut');
        $this->dropForeignKey('dok_prikaz_shablon_atribut_dok_prikaz_shablon_fkey', 'dok_prikaz_shablon_atribut');
        $this->dropForeignKey('dok_prikaz_polzovatel_fkey', 'dok_prikaz');
        $this->dropForeignKey('dok_prikaz_dok_prikaz_shablon_fkey', 'dok_prikaz');
        $this->dropForeignKey('dok_dok_prikaz_fkey', 'dok');
        $this->dropForeignKey('dok_roli_dolzhnost_fkey', 'dok_roli');
        $this->dropForeignKey('dok_roli_strukturnoe_podrazdelenie_fkey', 'dok_roli');
        $this->dropForeignKey('dok_process_dok_fkey', 'dok_process');
        $this->dropForeignKey('dok_process_dok_roli_fkey', 'dok_process');
        $this->dropForeignKey('dok_process_ispolnil_fiz_lico_fkey', 'dok_process');
        $this->dropForeignKey('dok_process_sozdal_fiz_lico_fkey', 'dok_process');

        $this->dropTable('dok_prikaz_tablica');
        $this->dropTable('dok_prikaz_atribut');
        $this->dropTable('dok_prikaz_shablon_ispolnitel');
        $this->dropTable('dok_prikaz_shablon_soglasovanie');
        $this->dropTable('dok_prikaz_shablon_atribut');
        $this->dropTable('dok_prikaz_shablon');
        $this->dropTable('dok_prikaz');
        $this->dropTable('dok_spisok_atributov');
        $this->dropTable('dok');
        $this->dropTable('dok_roli');
        $this->dropTable('dok_process');
    }
}
