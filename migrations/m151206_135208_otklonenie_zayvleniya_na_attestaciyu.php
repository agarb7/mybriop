<?php

use yii\db\Schema;
use yii\db\Migration;

class m151206_135208_otklonenie_zayvleniya_na_attestaciyu extends Migration
{
    public function up()
    {
        $this->createTable('otklonenie_zayavleniya_na_attestaciyu',[
            'id' => Schema::TYPE_PK,
            'nazvanie' => Schema::TYPE_STRING.' NOT NULL ',
            'text' => 'squeezed_text NOT NULL'
        ]);

        $this->insert('otklonenie_zayavleniya_na_attestaciyu',[
            'nazvanie'=>'Бумажный вариант не зарегистрирован',
            'text' => \app\globals\ApiGlobals::to_trimmed_text('Бумажный вариант вашего заявления в отделе'.
                ' аттестации не зарегистрирован. Свяжитесь с ответственным РУО, если '.
                'Ваша образовательная организация относится к городу Улан-Удэ, то '.
                'позвоните в МОЦОКО тел.:45-42-89 или в отдел аттестации по тел.: 44-31-04.')
        ]);

        $this->insert('otklonenie_zayavleniya_na_attestaciyu',[
            'nazvanie'=>'Отказ в установлении высшей квалификационной категории (не ранее чем через 2 года)',
            'text' => \app\globals\ApiGlobals::to_trimmed_text('с 15 июня 2014 г. вступил в силу новый порядок '.
                'проведения аттестации педагогических работников организаций, '.
                'осуществляющих образовательную деятельность, утвержденный '.
                'приказом Минобрнауки РФ № 276 от 7 апреля 2014 года. На основании '.
                'утвержденного приказа заявления о проведении аттестации в целях '.
                'установления высшей квалификационной категории по должности, по '.
                'которой аттестация будет проводиться впервые, подаются '.
                'педагогическими работниками не ранее чем через два года после '.
                'установления по этой должности первой квалификационной категории.')
        ]);

        $this->insert('otklonenie_zayavleniya_na_attestaciyu',[
            'nazvanie'=>'Отказ в установлении квалификационной категории',
            'text' => \app\globals\ApiGlobals::to_trimmed_text('с 15 июня 2014 г. вступил в силу новый порядок проведения аттестации'.
                ' педагогических работников организаций, осуществляющих '.
                'образовательную деятельность, утвержденный приказом Минобрнауки '.
                'РФ № 276 от 7 апреля 2014 года. После прохождения аттестационных '.
                'процедур, если Вам было отказано в установлении квалификационной '.
                'категории, то по новому  порядку аттестации Ваше заявление '.
                'принимается не раннее чем через год  с даты принятия решения аттестационной комиссии.')
        ]);
    }

    public function down()
    {
        echo "m151206_135208_otklonenie_zayvleniya_na_attestaciyu cannot be reverted.\n";

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
