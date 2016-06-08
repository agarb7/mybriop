<?php

use yii\db\Schema;
use yii\db\Migration;

class m160608_134928_update_otraslevoe_soglashenie extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE public.otraslevoe_soglashenie ADD tip_nazvanie varchar(512) NULL;');
        $this->execute('UPDATE otraslevoe_soglashenie SET tip_nazvanie = \'Государственная награда\' where tip = \'gos_nagrada\'');
        $this->execute('UPDATE otraslevoe_soglashenie SET tip_nazvanie = \'Почетное звание\' where tip = \'pochetnoe_zvanie\'');
        $this->execute('UPDATE otraslevoe_soglashenie SET tip_nazvanie = \'Ученая степень\' where tip = \'uchenaya_stepen\'');
        $this->execute('UPDATE otraslevoe_soglashenie SET tip_nazvanie = \'Победитель конкурса\' where tip = \'pobeditel_konkursa\'');
        $this->execute('UPDATE otraslevoe_soglashenie SET tip_nazvanie = \'Подготовка победителей/призеров предметных олимпиад/конкурсов в течение 5 лет\' where tip = \'podgotovka_prizerov_olimpiad\'');
        $this->execute('UPDATE otraslevoe_soglashenie SET tip_nazvanie = \'Подготовка призеров соревнований\' where tip = \'podgotovka_prizerov_sorevnovanij\'');
        $this->execute('UPDATE otraslevoe_soglashenie SET tip_nazvanie = \'Участие в проведении профессиональной экспетизы в течение 3 лет\' where tip = \'provedenie_prof_ekspertizy\'');
        $this->execute('UPDATE otraslevoe_soglashenie SET nazvanie = \'международный уровень\' WHERE nazvanie = \'международный\'');
        $this->execute('UPDATE otraslevoe_soglashenie SET nazvanie = \'всероссийский уровень\' WHERE nazvanie = \'всероссийский\'');
        $this->execute('UPDATE otraslevoe_soglashenie SET nazvanie = \'республиканский уровень\' WHERE nazvanie = \'республиканский\'');
    }

    public function down()
    {
        echo "m160608_134928_update_otraslevoe_soglashenie cannot be reverted.\n";

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
