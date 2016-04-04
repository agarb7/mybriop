<?php

use yii\db\Schema;
use yii\db\Migration;

class m160403_051113_ostenochnyj_list_status extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160403_051113_ostenochnyj_list_status cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->execute('CREATE TYPE status_otsenochnogo_lista as ENUM
                        (\'redaktiruetsya\',\'zapolneno\');');
        $this->addColumn('otsenochnyj_list_zayavleniya','status','status_otsenochnogo_lista NOT NULL default \'redaktiruetsya\'');
        $comment = <<<TEXT
        Статус оценочного листа заявления
        - оценочный лист находится в стадии редактирования, аттестационная комиссия может проставить оценки (redaktiruetsya),
        - лист заполнен, редактирование оценок доступно только председателю комиссии (zapolneno),
TEXT;
        $this->execute("comment on type status_otsenochnogo_lista is '$comment'");
    }

    public function safeDown()
    {
        echo "m160403_051113_ostenochnyj_list_status cannot be reverted.\n";
        return false;
    }

}
