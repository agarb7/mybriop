<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_004601_daty_attestacii_2016 extends Migration
{
//    public function up()
//    {
//
//    }
//
//    public function down()
//    {
//        echo "m160512_004601_daty_attestacii_2016 cannot be reverted.\n";
//
//        return false;
//    }


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20160601', 'priem_zayavleniya_konec'=> '20160630', 'nachalo'=>'20160901', 'konec'=> '20160930']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20160720', 'priem_zayavleniya_konec'=> '20160819', 'nachalo'=>'20161001', 'konec'=> '20161031']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20160820', 'priem_zayavleniya_konec'=> '20160919', 'nachalo'=>'20161101', 'konec'=> '20161130']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20160920', 'priem_zayavleniya_konec'=> '20161019', 'nachalo'=>'20161201', 'konec'=> '20161231']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20161020', 'priem_zayavleniya_konec'=> '20161119', 'nachalo'=>'20170101', 'konec'=> '20170131']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20161120', 'priem_zayavleniya_konec'=> '20161219', 'nachalo'=>'20170201', 'konec'=> '20170228']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20161220', 'priem_zayavleniya_konec'=> '20170119', 'nachalo'=>'20170301', 'konec'=> '20170331']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20170120', 'priem_zayavleniya_konec'=> '20170219', 'nachalo'=>'20170401', 'konec'=> '20170430']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20170220', 'priem_zayavleniya_konec'=> '20170319', 'nachalo'=>'20170501', 'konec'=> '20170531']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20170601', 'priem_zayavleniya_konec'=> '20170630', 'nachalo'=>'20170901', 'konec'=> '20170930']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20170720', 'priem_zayavleniya_konec'=> '20170819', 'nachalo'=>'20171001', 'konec'=> '20171031']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20170820', 'priem_zayavleniya_konec'=> '20170919', 'nachalo'=>'20171101', 'konec'=> '20171130']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20170920', 'priem_zayavleniya_konec'=> '20171019', 'nachalo'=>'20171201', 'konec'=> '20171231']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20171020', 'priem_zayavleniya_konec'=> '20171119', 'nachalo'=>'20180101', 'konec'=> '20180131']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20171120', 'priem_zayavleniya_konec'=> '20171219', 'nachalo'=>'20180201', 'konec'=> '20180228']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20171220', 'priem_zayavleniya_konec'=> '20180119', 'nachalo'=>'20180301', 'konec'=> '20180331']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20180120', 'priem_zayavleniya_konec'=> '20180219', 'nachalo'=>'20180401', 'konec'=> '20180430']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20180220', 'priem_zayavleniya_konec'=> '20180319', 'nachalo'=>'20180501', 'konec'=> '20180531']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20180601', 'priem_zayavleniya_konec'=> '20180630', 'nachalo'=>'20180901', 'konec'=> '20180930']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20180720', 'priem_zayavleniya_konec'=> '20180819', 'nachalo'=>'20181001', 'konec'=> '20181031']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20180820', 'priem_zayavleniya_konec'=> '20180919', 'nachalo'=>'20181101', 'konec'=> '20181130']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20180920', 'priem_zayavleniya_konec'=> '20181019', 'nachalo'=>'20181201', 'konec'=> '20181231']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20181020', 'priem_zayavleniya_konec'=> '20181119', 'nachalo'=>'20190101', 'konec'=> '20190131']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20181120', 'priem_zayavleniya_konec'=> '20181219', 'nachalo'=>'20190201', 'konec'=> '20190228']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20181220', 'priem_zayavleniya_konec'=> '20190119', 'nachalo'=>'20190301', 'konec'=> '20190331']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20190120', 'priem_zayavleniya_konec'=> '20190219', 'nachalo'=>'20190401', 'konec'=> '20190430']);
        $this->insert('vremya_provedeniya_attestacii',['priem_zayavleniya_nachalo'=>'20190220', 'priem_zayavleniya_konec'=> '20190319', 'nachalo'=>'20190501', 'konec'=> '20190531']);
    }

    public function safeDown()
    {
        echo "m160512_004601_daty_attestacii_2016 cannot be reverted.\n";

        return false;
    }
}
