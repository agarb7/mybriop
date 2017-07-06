<?php

use yii\db\Migration;

class m170618_151418_dok_prikaz_tablica_add_column_dokument extends Migration
{
    public function up()
    {
        $this->addColumn('dok_prikaz_tablica', 'osnovanija', 'integer[] DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('dok_prikaz_tablica', 'osnovanija');
    }
}
