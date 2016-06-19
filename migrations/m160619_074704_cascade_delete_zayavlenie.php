<?php

use yii\db\Schema;
use yii\db\Migration;

class m160619_074704_cascade_delete_zayavlenie extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE public.obrazovanie_dlya_zayavleniya_na_attestaciyu DROP CONSTRAINT obrazovanie_dlya_zayavleniya_na__zayavlenie_na_attestaciyu_fkey;');
        $sql = <<<SQL
ALTER TABLE public.obrazovanie_dlya_zayavleniya_na_attestaciyu
ADD CONSTRAINT obrazovanie_dlya_zayavleniya_na__zayavlenie_na_attestaciyu_fkey
FOREIGN KEY (zayavlenie_na_attestaciyu) REFERENCES zayavlenie_na_attestaciyu (id) ON DELETE CASCADE ON UPDATE CASCADE;
SQL;
        $this->execute($sql);
        $this->execute('ALTER TABLE public.otraslevoe_soglashenie_zayavleniya DROP CONSTRAINT otraslevoe_soglashenie_zayavleni_zayavlenie_na_attestaciyu_fkey;');
        $sql = <<<SQL
ALTER TABLE public.otraslevoe_soglashenie_zayavleniya
ADD CONSTRAINT otraslevoe_soglashenie_zayavleni_zayavlenie_na_attestaciyu_fkey
FOREIGN KEY (zayavlenie_na_attestaciyu) REFERENCES zayavlenie_na_attestaciyu (id) ON DELETE CASCADE ON UPDATE CASCADE;
SQL;
        $this->execute($sql);
        $this->execute('ALTER TABLE public.otsenochnyj_list_zayavleniya DROP CONSTRAINT otsenochnyj_list_zayavleniya_zayavlenie_na_attestaciyu_fkey;');
        $sql = <<<SQL
ALTER TABLE public.otsenochnyj_list_zayavleniya
ADD CONSTRAINT otsenochnyj_list_zayavleniya_zayavlenie_na_attestaciyu_fkey
FOREIGN KEY (zayavlenie_na_attestaciyu) REFERENCES zayavlenie_na_attestaciyu (id) ON DELETE CASCADE ON UPDATE CASCADE;
SQL;
        $this->execute($sql);
        $this->execute('ALTER TABLE public.struktura_otsenochnogo_lista_zayvaleniya DROP CONSTRAINT struktura_otsenochnogo_lista__otsenochnyj_list_zayavleniya_fkey;');
        $sql = <<<SQL
ALTER TABLE public.struktura_otsenochnogo_lista_zayvaleniya
ADD CONSTRAINT struktura_otsenochnogo_lista__otsenochnyj_list_zayavleniya_fkey
FOREIGN KEY (otsenochnyj_list_zayavleniya) REFERENCES otsenochnyj_list_zayavleniya (id) ON DELETE CASCADE ON UPDATE CASCADE;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160619_074704_cascade_delete_zayavlenie cannot be reverted.\n";

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
