<?php

use yii\db\Migration;

class m170529_131512_kadry_briop extends Migration
{
    public function up()
    {
        // текущее состояние
        $this->addColumn('dolzhnost_fiz_lica_na_rabote', 'actual', \yii\db\pgsql\Schema::TYPE_BOOLEAN);

        // центра развития профессионального образования id=5
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'rukovoditel_strukturnogo_podrazdeleniya' =>  false,
            'actual' => true,
            'dolzhnost' => 2786,
        ], 'id=33'); // Бадмаева Долгор Дамбиевна
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'rukovoditel_strukturnogo_podrazdeleniya' => true,
            'dolzhnost' => 47,
            'actual' => true,
        ], 'id=48'); // Степанец Ольга Викторовна

        // кафедра экономики, права и государственного управления id=1
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => false,
        ], 'id=42'); // Халудорова Любовь Енжаповна
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'rukovoditel_strukturnogo_podrazdeleniya' => true,
            'dolzhnost' => 47,
            'actual' => true,
        ], 'id=1'); // Доржиев Дандар Леонидович

        // центр методического сопровождения педагогических работников и образовательных организаций
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => false,
        ], 'id=73'); // Цепков Константин Антонович
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => false,
        ], 'id=74'); // Цепков Константин Антонович
    }

    public function down()
    {
        $this->dropColumn('dolzhnost_fiz_lica_na_rabote', 'actual');
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
