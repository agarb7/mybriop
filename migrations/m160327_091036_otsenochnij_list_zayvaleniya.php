<?php

use yii\db\Schema;
use yii\db\Migration;

class m160327_091036_otsenochnij_list_zayvaleniya extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160327_091036_otsenochnij_list_zayvaleniya cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->createTable('otsenochnyj_list_zayavleniya', [
            'id' => Schema::TYPE_PK,
            'otsenochnij_list' => Schema::TYPE_BIGINT. ' not null',
            'nazvanie' => 'squeezed_text NOT NULL',
            'min_ball_pervaya_kategoriya' => Schema::TYPE_INTEGER.' NULL',
            'min_ball_visshaya_kategoriya' => Schema::TYPE_INTEGER.' NULL',
            'rabotnik_komissii' => Schema::TYPE_BIGINT.' not null references fiz_lico(id)',
            'zayavlenie_na_attestaciyu' => Schema::TYPE_BIGINT.' not null references zayavlenie_na_attestaciyu(id)',
            'var_ispytanie_3' => Schema::TYPE_INTEGER.' NULL references attestacionnoe_variativnoe_ispytanie_3(id)',
            'postoyannoe_ispytanie' => Schema::TYPE_INTEGER.' NULL references postoyannoe_ispytanie(id)',
            'CHECK((var_ispytanie_3 IS NOT NULL) OR (postoyannoe_ispytanie IS NOT NULL))'=>''
        ]);

        $this->createTable('struktura_otsenochnogo_lista_zayvaleniya',[
            'id' => Schema::TYPE_PK,
            'otsenochnyj_list_zayavleniya' => Schema::TYPE_BIGINT.' not null references otsenochnyj_list_zayavleniya(id)',
            'nazvanie' => 'squeezed_text NOT NULL',
            'max_bally' => Schema::TYPE_INTEGER.' NOT NULL',
            'bally' => Schema::TYPE_INTEGER.' NULL',
            'nomer' => 'varchar(5) NOT NULL',
            'uroven' => Schema::TYPE_INTEGER.' not null'
        ]);
    }

    public function safeDown()
    {
        echo "m160327_091036_otsenochnij_list_zayvaleniya cannot be reverted.\n";
        return false;
    }

}
