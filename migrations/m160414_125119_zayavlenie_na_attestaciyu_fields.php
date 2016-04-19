<?php

use yii\db\Schema;
use yii\db\Migration;

class m160414_125119_zayavlenie_na_attestaciyu_fields extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160414_125119_zayavlenie_na_attestaciyu_fields cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('zayavlenie_na_attestaciyu', 'ld_olimpiady','squeezed_text NULL');
        $comment = 'Результаты участия обучающихся в предметных олимпиадах, конкурсах';
        $this->execute("comment on column zayavlenie_na_attestaciyu.ld_olimpiady is '$comment'");
        $this->addColumn('zayavlenie_na_attestaciyu', 'ld_posobiya','squeezed_text NULL');
        $comment = 'Наличие опубликованных собственных методических разработок, методических материалов (программ, учебных и учебно-методических пособий, диагностических материалов, цифровых образовательных ресурсов), прошедших независимую экспертизу, имеющих соответствующий гриф и выходные данные';
        $this->execute("comment on column zayavlenie_na_attestaciyu.ld_posobiya is '$comment'");
        $this->addColumn('zayavlenie_na_attestaciyu', 'ld_publikacii','squeezed_text NULL');
        $comment = 'Наличие опубликованных статей, научных публикаций, имеющих соответствующий гриф и выходные данные';
        $this->execute("comment on column zayavlenie_na_attestaciyu.ld_publikacii is '$comment'");
        $this->addColumn('zayavlenie_na_attestaciyu', 'ld_prof_konkursy','squeezed_text NULL');
        $comment = 'Результативность участия в профессиональных конкурсах';
        $this->execute("comment on column zayavlenie_na_attestaciyu.ld_prof_konkursy is '$comment'");
        $this->addColumn('zayavlenie_na_attestaciyu', 'ld_obshestvennaya_aktivnost','squeezed_text NULL');
        $comment = 'Общественная активность педагога: участие в экспертных комиссиях, предметных комиссиях (ЕГЭ, ГИА), в жюри конкурсов, творческих группах';
        $this->execute("comment on column zayavlenie_na_attestaciyu.ld_obshestvennaya_aktivnost is '$comment'");
        $this->addColumn('zayavlenie_na_attestaciyu', 'ld_elektronnye_resursy','squeezed_text NULL');
        $comment = 'Использование электронных образовательных ресурсов (ЭОР) в образовательном процессе';
        $this->execute("comment on column zayavlenie_na_attestaciyu.ld_elektronnye_resursy is '$comment'");
        $this->addColumn('zayavlenie_na_attestaciyu', 'ld_otkrytoe_meropriyatie','squeezed_text NULL');
        $comment = 'Публичное представление собственного педагогического опыта в форме открытого мероприятия';
        $this->execute("comment on column zayavlenie_na_attestaciyu.ld_otkrytoe_meropriyatie is '$comment'");
        $this->addColumn('zayavlenie_na_attestaciyu', 'ld_nastavnik','squeezed_text NULL');
        $comment = 'Исполнение функций наставника';
        $this->execute("comment on column zayavlenie_na_attestaciyu.ld_nastavnik is '$comment'");
        $this->addColumn('zayavlenie_na_attestaciyu', 'ld_deti_sns','squeezed_text NULL');
        $comment = 'Работа с детьми из СНС (социально неблагополучных семей)';
        $this->execute("comment on column zayavlenie_na_attestaciyu.ld_deti_sns is '$comment'");
    }

    public function safeDown()
    {
        echo "m160414_125119_zayavlenie_na_attestaciyu_fields cannot be reverted.\n";

        return false;
    }

}
