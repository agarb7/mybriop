<?php

use yii\db\Schema;
use yii\db\Migration;

class m160307_061713_otraslevoe_soglashenie2 extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160307_061713_otraslevoe_soglashenie2 cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->execute('CREATE TYPE tip_otraslevogo_soglashenijya AS ENUM
        (\'gos_nagrada\',\'pochetnoe_zvanie\', \'pobeditel_konkursa\', \'uchenaya_stepen\',
        \'podgotovka_prizerov_olimpiad\',\'podgotovka_prizerov_sorevnovanij\',\'provedenie_prof_ekspertizy\');');
       $this->addColumn('otraslevoe_soglashenie','tip','tip_otraslevogo_soglashenijya NOT NULL');

        $data =[
            'медаль ордена "За заслуги перед Отечеством"',
            'медаль "За вклад в развитие в образование"'
        ];

        foreach ($data as $item) {
            $this->insert('otraslevoe_soglashenie',[
                'nazvanie' => $item,
                'tip' => 'gos_nagrada'
            ]);
        }

        $data =[
            'заслуженный учитель РФ',
            'заслуженный деятель науки РБ',
            'заслуженный деятель науки РФ',
            'заслуженный работник физической культуры РФ',
            'заслуженный работник физической культуры РФ',
            'отличник физической культуры и спорта РФ',
            'отличник физической культуры и спорта РБ',
            'отличник просвещения СССР',
            'отличник народного просвещения РФ',
            'почетный работник общего образования РФ',
            'почетный работник среднего профессионального образования РФ',
            'народный учитель РБ',
            'заслуженный учитель РБ',
            'заслуженный работник образования РБ',
            'заслуженный работник физической культуры РБ',
        ];

        foreach ($data as $item) {
            $this->insert('otraslevoe_soglashenie',[
                'nazvanie' => $item,
                'tip' => 'pochetnoe_zvanie'
            ]);
        }

        $data = [
            'лучшие учителя России',
            'лучшие учителя Бурятии',
            'всероссийских конкурсов профессионального мастерства, учредителем которых победители является Министерство образования и науки Российской Федерации и победители Общероссийский профсоюз образования',
            'республиканских конкурсов профессионального мастерства'
        ];

        foreach ($data as $item) {
            $this->insert('otraslevoe_soglashenie',[
                'nazvanie' => $item,
                'tip' => 'pobeditel_konkursa'
            ]);
        }

        $data = [
            'доктор архитектуры',
            'доктор биологических наук',
            'доктор ветеринарных наук',
            'доктор военных наук',
            'доктор географических наук',
            'доктор геолого-минералогических наук',
            'доктор искусствоведения',
            'доктор исторических наук',
            'доктор культурологии',
            'доктор медицинских наук',
            'доктор педагогических наук',
            'доктор политических наук',
            'доктор психологических наук',
            'доктор социологических наук',
            'доктор сельскохозяйственных наук',
            'доктор технических наук',
            'доктор фармацевтических наук',
            'доктор физико-математических наук',
            'доктор филологических наук',
            'доктор философских наук',
            'доктор химических наук',
            'доктор экономических наук',
            'доктор юридических наук',
            'кандидат архитектуры',
            'кандидат биологических наук',
            'кандидат ветеринарных наук',
            'кандидат военных наук',
            'кандидат географических наук',
            'кандидат геолого-минералогических наук',
            'кандидат искусствоведения',
            'кандидат исторических наук',
            'кандидат культурологии',
            'кандидат медицинских наук',
            'кандидат педагогических наук',
            'кандидат политических наук',
            'кандидат психологических наук',
            'кандидат социологических наук',
            'кандидат сельскохозяйственных наук',
            'кандидат технических наук',
            'кандидат фармацевтических наук',
            'кандидат физико-математических наук',
            'кандидат филологических наук',
            'кандидат философских наук',
            'кандидат химических наук',
            'кандидат экономических наук',
            'кандидат юридических наук'
        ];

        foreach ($data as $item) {
            $this->insert('otraslevoe_soglashenie',[
                'nazvanie' => $item,
                'tip' => 'uchenaya_stepen'
            ]);
        }

        $data = [
            'международный',
            'всероссийский',
            'республиканский'
        ];

        foreach ($data as $item) {
            $this->insert('otraslevoe_soglashenie',[
                'nazvanie' => $item,
                'tip' => 'podgotovka_prizerov_olimpiad'
            ]);
        }

        $data = [
            'чемпионат России',
            'первенство России',
            'спартакиада России',
            'чемпионат Европы',
            'первенство Европы',
            'чемпионат Мира',
            'первенство Мира',
            'победителей Всероссийских соревнований, проводимых ДРСФСВ МОиН РФ'
        ];

        foreach ($data as $item) {
            $this->insert('otraslevoe_soglashenie',[
                'nazvanie' => $item,
                'tip' => 'podgotovka_prizerov_sorevnovanij'
            ]);
        }

        $data = [
            \app\globals\ApiGlobals::to_trimmed_text('участие в проведении
            проф. экспертизы в составе экспертно-профильных групп при Аттестационной
            комиссии Министерства образования и науки Республики Бурятия не менее трех
            лет в период, предшествующий аттестацииучаствовавшие в проведении
            профессиональной экспертизы в составе экспертно-профильных групп
            при Аттестационной комиссии Министерства образования и науки Республики
            Бурятия не менее трех лет в период, предшествующий аттестации')
        ];

        foreach ($data as $item) {
            $this->insert('otraslevoe_soglashenie',[
                'nazvanie' => $item,
                'tip' => 'provedenie_prof_ekspertizy'
            ]);
        }

        $this->createTable('otraslevoe_soglashenie_zayavleniya',[
            'id' => Schema::TYPE_PK,
            'otraslevoe_soglashenie' => Schema::TYPE_BIGINT.' NOT NULL references otraslevoe_soglashenie(id)',
            'zayavlenie_na_attestaciyu' => Schema::TYPE_BIGINT.' NOT NULL references zayavlenie_na_attestaciyu(id)',
            'fajl' => Schema::TYPE_BIGINT.' NULL references fajl(id)'
        ]);
    }

    public function safeDown()
    {
    }

}
