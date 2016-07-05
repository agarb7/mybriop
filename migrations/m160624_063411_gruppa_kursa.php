<?php

use yii\db\Migration;

class m160624_063411_gruppa_kursa extends Migration
{
    public function up()
    {
        $sql=<<<SQL

CREATE TABLE gruppa_kursa (
    id integer NOT NULL,
    gruppa character varying(6),
    kurs integer,
    roditel integer
);

ALTER TABLE public.gruppa_kursa OWNER TO mybriop;

CREATE SEQUENCE gruppa_kursa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE public.gruppa_kursa_id_seq OWNER TO mybriop;

ALTER SEQUENCE gruppa_kursa_id_seq OWNED BY gruppa_kursa.id;

CREATE SEQUENCE gruppa_kursa_roditel_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE public.gruppa_kursa_roditel_seq OWNER TO mybriop;

ALTER SEQUENCE gruppa_kursa_roditel_seq OWNED BY gruppa_kursa.roditel;

ALTER TABLE ONLY gruppa_kursa ALTER COLUMN id SET DEFAULT nextval('gruppa_kursa_id_seq'::regclass);

ALTER TABLE ONLY gruppa_kursa
    ADD CONSTRAINT gruppa_kursa_pkey PRIMARY KEY (id);

CREATE UNIQUE INDEX gruppa ON gruppa_kursa USING btree (gruppa);

ALTER TABLE ONLY gruppa_kursa
    ADD CONSTRAINT gruppa_kursa_kurs_fkey FOREIGN KEY (kurs) REFERENCES kurs(id);

ALTER TABLE ONLY gruppa_kursa
    ADD CONSTRAINT gruppa_kursa_roditel_fkey FOREIGN KEY (roditel) REFERENCES gruppa_kursa(id);

SQL;
        $this->execute($sql);

        $sql=<<<SQL
ALTER TABLE kurs_fiz_lica 
    ADD COLUMN gruppa_kursa bigint;

ALTER TABLE ONLY kurs_fiz_lica
    ADD CONSTRAINT kurs_fiz_lica_gruppa_kursa_fkey FOREIGN KEY (gruppa_kursa) REFERENCES gruppa_kursa(id);

SQL;
        $this->execute($sql);

    }

    public function down()
    {
        echo "m160624_063411_gruppa_kursa cannot be reverted.\n";

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
