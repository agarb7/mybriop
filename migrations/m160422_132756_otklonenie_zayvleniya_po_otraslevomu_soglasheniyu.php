<?php

use yii\db\Schema;
use yii\db\Migration;

class m160422_132756_otklonenie_zayvleniya_po_otraslevomu_soglasheniyu extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160422_132756_otklonenie_zayvleniya_po_otraslevomu_soglasheniyu cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $text = <<<TEXT
На основании "Отраслевого соглашения" в пункте 8.16. в разделе "Аттестация" перечислены категории педагогических работников, которые могут проходить аттестацию без вариативных аттестационных процедур и защиты системы педагогической деятельности. В связи с отсутствием подтверждающих документов по критериям отраслевого соглашения Вам необходимо выбрать вариативные испытания процедуры аттестации. (см. на сайте briop.ru "ОТРАСЛЕВОЕ СОГЛАШЕНИЕ" между Рескомом профсоюза работников народного образования и науки РФ и Министерством образования и науки Республики Бурятия. Тел.:443104; 580120).
TEXT;
        $this->insert('otklonenie_zayavleniya_na_attestaciyu',['nazvanie'=>'Отраслевое соглашение','text'=>$text]);
    }

    public function safeDown()
    {
        echo "m160422_132756_otklonenie_zayvleniya_po_otraslevomu_soglasheniyu cannot be reverted.\n";

        return false;
    }

}
