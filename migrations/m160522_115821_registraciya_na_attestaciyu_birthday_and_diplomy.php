<?php

use yii\db\Schema;
use yii\db\Migration;

class m160522_115821_registraciya_na_attestaciyu_birthday_and_diplomy extends Migration
{
    public function up()
    {
        $this->addColumn('zayavlenie_na_attestaciyu','data_rozhdeniya', Schema::TYPE_DATE);

        $this->execute('ALTER TYPE tip_dokumenta_ob_obrazovanii add value \'dip\' AFTER \'dip_mag\'');
        $this->execute('ALTER TYPE tip_dokumenta_ob_obrazovanii add value \'udost\' AFTER \'dip\'');
    }

    public function down()
    {
        echo "m160522_115821_registraciya_na_attestaciyu_birthday_and_diplomy cannot be reverted.\n";

        return false;
    }

//    public function safeUp()
//    {
//
//    }
//
//    public function safeDown()
//    {
//        echo "m160522_115821_registraciya_na_attestaciyu_birthday_and_diplomy cannot be reverted.\n";
//
//        return false;
//    }
}
