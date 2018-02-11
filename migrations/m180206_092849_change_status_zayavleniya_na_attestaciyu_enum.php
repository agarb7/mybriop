<?php

use yii\db\Migration;

/**
 * Class m180206_092849_change_status_zayavleniya_na_attestaciyu_enum
 */
class m180206_092849_change_status_zayavleniya_na_attestaciyu_enum extends Migration
{

    public function up()
    {
        $this->execute('alter type status_zayavleniya_na_attestaciyu add value \'zablokirovano_otdelom_attestacii\'');
    }

    public function down()
    {
        echo "m180206_092849_change_status_zayavleniya_na_attestaciyu_enum cannot be reverted.\n";

        return false;
    }
   
}
