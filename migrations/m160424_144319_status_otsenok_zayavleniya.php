<?php

use yii\db\Schema;
use yii\db\Migration;

class m160424_144319_status_otsenok_zayavleniya extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160424_144319_status_otsenok_zayavleniya cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->execute('CREATE TYPE status_otsenok_zayavleniya as ENUM
                        (\'redaktiruetsya\',\'podpisano\');');
        $this->addColumn('raspredelenie_zayavlenij_na_attestaciyu','status','status_otsenok_zayavleniya NOT NULL default \'redaktiruetsya\'');
        $comment = <<<TEXT
        Статус оценочного листа заявления
        - Руководитель может сбрасывать оценки, оценки с этим статусом не идут в общий зачет (redaktiruetsya),
        - Оценки не редактируются, оценки идут в зачет (podpisano),
TEXT;
        $this->execute("comment on type status_otsenok_zayavleniya is '$comment'");
    }

    public function safeDown()
    {
        echo "m160424_144319_status_otsenok_zayavleniya cannot be reverted.\n";

        return false;
    }

}
