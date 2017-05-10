<?php

use yii\db\Migration;

class m170428_012554_add_ik_fgos_to_zayavlenie_na_attestaciyu extends Migration
{
    public function safeUp()
    {
        $this->addColumn('zayavlenie_na_attestaciyu','is_fgos',\yii\db\pgsql\Schema::TYPE_BOOLEAN);
        $this->addColumn('zayavlenie_na_attestaciyu','informacionnaja_karta', \yii\db\pgsql\Schema::TYPE_INTEGER);
        $this->addForeignKey(
            'zayavlenie_na_attestaciyu_fajl_fkey',
            'zayavlenie_na_attestaciyu',
            'informacionnaja_karta',
            'fajl',
            'id'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('zayavlenie_na_attestaciyu_fajl_fkey', 'zayavlenie_na_attestaciyu');
        $this->dropColumn('zayavlenie_na_attestaciyu', 'is_fgos');
        $this->dropColumn('zayavlenie_na_attestaciyu', 'informacionnaja_karta');
    }
}
