<?php

use yii\db\Migration;

class m170530_071437_kadry_briop_2 extends Migration
{
    public function up()
    {
        /**
         * кафедра развития образовательных систем id=3
         */
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => false,
        ], 'id=142'); // Чердонова Вероника Александровна
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'rukovoditel_strukturnogo_podrazdeleniya' => true,
            'dolzhnost' => 47,
            'actual' => true,
        ], 'id=18'); // Алексеева Надежда Николаевна
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
        ], 'id=15'); // Гармажапова Лариса Алексеевна
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
        ], 'id=14'); // Сенгеева Татьяна Николаевна

        /**
         * кафедра экономики, права и государственного управления id=1
         */
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
        ], 'id=43'); // Халудорова Любовь Енжаповна
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
        ], 'id=50'); // Григорьева Аюна Ринчиндоржиевна

        /**
         * кафедра развития технологии филологического образования id=16
         */
        $this->insert('dolzhnost_fiz_lica_na_rabote', [
            'rabota_fiz_lica' => 42,
            'strukturnoe_podrazdelenie' => 16,
            'dolzhnost' => 6,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => true,
        ]); // Халудорова Любовь Янжаповна
        $this->insert('dolzhnost_fiz_lica_na_rabote', [
            'rabota_fiz_lica' => 15,
            'strukturnoe_podrazdelenie' => 16,
            'dolzhnost' => 47,
            'rukovoditel_strukturnogo_podrazdeleniya' => true,
            'actual' => true,
        ]); // Гармажапова Лариса Алексеевна

        /**
         * лаборатория коррекционного и инклюзивного образования id=17
         */
        $this->insert('dolzhnost_fiz_lica_na_rabote', [
            'rabota_fiz_lica' => 142,
            'strukturnoe_podrazdelenie' => 17,
            'dolzhnost' => 47,
            'rukovoditel_strukturnogo_podrazdeleniya' => true,
            'actual' => true,
        ]); // Чердонова Вероника Александровна
        $this->insert('dolzhnost_fiz_lica_na_rabote', [
            'rabota_fiz_lica' => 14,
            'strukturnoe_podrazdelenie' => 17,
            'dolzhnost' => 845,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => true,
        ]); // Сенгеева Татьяна Николаевна
        $this->insert('dolzhnost_fiz_lica_na_rabote', [
            'rabota_fiz_lica' => 8,
            'strukturnoe_podrazdelenie' => 17,
            'dolzhnost' => 845,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => true,
        ]); // Телешева Ирина Александровна

        /**
         * кафедра инновационного проектирования id=2
         */
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
        ], 'id=8'); // Телешева Ирина Александровна

        /**
         * лаборатория развивающего образования id=6
         */
        $this->update('dolzhnost_fiz_lica_na_rabote', [
            'actual' => false,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
        ], 'id=49'); // Григорьева Аюна Ринчиндоржиевна

        /**
         * центр развития профессионального образования id=5
         */
        $this->insert('dolzhnost_fiz_lica_na_rabote', [
            'rabota_fiz_lica' => 49,
            'strukturnoe_podrazdelenie' => 5,
            'dolzhnost' => 845,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => true,
        ]); // Григорьева Аюна Ринчиндоржиевна

        /**
         * центр методического сопровождения педагогических работников и образовательных организаций id=4
         */
        $this->insert('dolzhnost_fiz_lica_na_rabote', [
            'rabota_fiz_lica' => 8,
            'strukturnoe_podrazdelenie' => 4,
            'dolzhnost' => 845,
            'rukovoditel_strukturnogo_podrazdeleniya' => false,
            'actual' => true,
        ]); // Телешева Ирина Александровна
    }

    public function down()
    {
        echo "m170530_071437_kadry_briop_2 cannot be reverted.\n";

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
