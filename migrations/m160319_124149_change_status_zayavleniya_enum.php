<?php

use yii\db\Schema;
use yii\db\Migration;

class m160319_124149_change_status_zayavleniya_enum extends Migration
{
    public function up()
    {
        $this->execute('alter type status_zayavleniya_na_attestaciyu add value \'v_otdele_attestacii\' AFTER \'otkloneno\'');
    }

    public function down()
    {
        echo "m160319_124149_change_status_zayavleniya_enum cannot be reverted.\n";

        return false;
    }


//    // Use safeUp/safeDown to run migration code within a transaction
//    public function safeUp()
//    {
//
//    }
//
//    public function safeDown()
//    {
//    }

}
