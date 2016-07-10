<?php

use yii\db\Schema;
use yii\db\Migration;

class m160710_112829_preset_otkloneniya_po_mestu_raboty extends Migration
{
    // public function up()
    // {

    // }

    // public function down()
    // {
    //     echo "m160710_112829_preset_otkloneniya_po_mestu_raboty cannot be reverted.\n";

    //     return false;
    // }

    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
         $this->insert('otklonenie_zayavleniya_na_attestaciyu',[
            'nazvanie'=>'Неверно заполнено место работы',
            'text' => \app\globals\ApiGlobals::to_trimmed_text('Уточните данные о месте работы: район или город. Редактировние доступно в разделе "Мои данные"->"Работа".')
        ]);
    }

    public function safeDown()
    {
    }
    
}
