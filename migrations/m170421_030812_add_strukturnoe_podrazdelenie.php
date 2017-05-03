<?php

use yii\db\Migration;

class m170421_030812_add_strukturnoe_podrazdelenie extends Migration
{
    public function safeUp()
    {
        $this->insert('strukturnoe_podrazdelenie', [
            'id' => 18,
            'organizaciya' => 1,
            'nazvanie' => 'ректорат',
            'obschij' => true,
        ]);
    }

    public function safeDown()
    {
        $this->delete('strukturnoe_podrazdelenie', ['id' => 18]);
    }
}
