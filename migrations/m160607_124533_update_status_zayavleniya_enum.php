<?php

use yii\db\Schema;
use yii\db\Migration;

class m160607_124533_update_status_zayavleniya_enum extends Migration
{
    public function up()
    {
        //$this->execute('alter type status_zayavleniya_na_attestaciyu
        //                add value \'podpisano_otdelom_attestacii\' AFTER \'v_otdele_attestacii\'');
        $this->execute('UPDATE zayavlenie_na_attestaciyu
                        set status = \'podpisano_otdelom_attestacii\'
                        WHERE status = \'podpisano_ped_rabotnikom\'');
        $this->execute('CREATE TYPE status_zayavleniya_na_attestaciyu_new as
        ENUM(
            \'redaktiruetsya_ped_rabotnikom\',
            \'otkloneno\',
            \'v_otdele_attestacii\',
            \'podpisano_otdelom_attestacii\'
        )');
        $this->execute('ALTER TABLE zayavlenie_na_attestaciyu
                        ALTER COLUMN status DROP DEFAULT');
        $this->execute('DELETE FROM zayavlenie_na_attestaciyu WHERE status = \'podpisano_ped_rabotnikom\';');
        $this->execute('ALTER TABLE zayavlenie_na_attestaciyu
                        ALTER COLUMN status TYPE status_zayavleniya_na_attestaciyu_new
                        USING (status::text::status_zayavleniya_na_attestaciyu_new);');
        $this->execute('DROP TYPE status_zayavleniya_na_attestaciyu;');
        $this->execute('ALTER TYPE status_zayavleniya_na_attestaciyu_new RENAME TO status_zayavleniya_na_attestaciyu;');
        $this->execute('ALTER TABLE zayavlenie_na_attestaciyu
                        ALTER COLUMN status SET DEFAULT
                       \'redaktiruetsya_ped_rabotnikom\'::status_zayavleniya_na_attestaciyu');
    }

    public function down()
    {
        echo "m160607_124533_update_status_zayavleniya_enum cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
