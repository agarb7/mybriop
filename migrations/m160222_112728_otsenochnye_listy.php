<?php

use yii\db\Schema;
use yii\db\Migration;

class m160222_112728_otsenochnye_listy extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160222_112728_otsenochnye_listy cannot be reverted.\n";
//
//        return false;
//    }

    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    $this->createTable('otsenochnyj_list',[
            'id' => Schema::TYPE_PK,
            'nazvanie' => 'squeezed_text NOT NULL',
            'min_ball_pervaya_kategoriya' => Schema::TYPE_INTEGER.' NULL',
            'min_ball_visshaya_kategoriya' => Schema::TYPE_INTEGER.' NULL',
        ]);

        $this->createTable('struktura_otsenochnogo_lista',[
            'id' => Schema::TYPE_PK,
            'otsenochnyj_list' => Schema::TYPE_BIGINT.' not null references otsenochnyj_list(id)',
            'nazvanie' => 'squeezed_text NOT NULL',
            'bally' => Schema::TYPE_INTEGER.' NOT NULL',
            'nomer' => Schema::TYPE_INTEGER.' NOT NULL',
            'roditel' => Schema::TYPE_BIGINT.' NULL references struktura_otsenochnogo_lista(id)'
        ]);

        $this->createTable('postoyannoe_ispytanie',[
           'id' => Schema::TYPE_PK,
            'nazvanie' => 'squeezed_text NOT NULL',
            'pervaya_kategoriya' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE',
            'vysshaya_kategoriya' => Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT FALSE'
        ]);

        $this->insert('postoyannoe_ispytanie',[
            'nazvanie' => 'Портфолио',
            'pervaya_kategoriya' => true,
            'vysshaya_kategoriya' => true
        ]);

        $this->insert('postoyannoe_ispytanie',[
            'nazvanie' => 'СПД',
            'pervaya_kategoriya' => false,
            'vysshaya_kategoriya' => true
        ]);

        $this->createTable('ispytanie_otsenochnogo_lista',[
            'id' => Schema::TYPE_PK,
            'otsenochnyj_list' => Schema::TYPE_BIGINT.' NOT NULL references otsenochnyj_list(id)',
            'var_ispytanie_3' => Schema::TYPE_INTEGER.' NULL references attestacionnoe_variativnoe_ispytanie_3(id)',
            'postoyannoe_ispytanie' => Schema::TYPE_INTEGER.' NULL references postoyannoe_ispytanie(id)',
            'CHECK((var_ispytanie_3 IS NOT NULL) OR (postoyannoe_ispytanie IS NOT NULL))'=>''
        ]);
    }

    public function safeDown()
    {
        echo "m160222_112728_otsenochnye_listy cannot be reverted.\n";

        return false;
    }

}
