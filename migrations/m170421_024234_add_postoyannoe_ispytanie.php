<?php

use yii\db\Migration;

class m170421_024234_add_postoyannoe_ispytanie extends Migration
{
    public function safeUp()
    {
        $this->insert('postoyannoe_ispytanie', [
            'id' => 3,
            'nazvanie' => 'ИК первой категории по ФГОС',
            'pervaya_kategoriya' => true,
            'vysshaya_kategoriya' => false,
        ]);
        $this->insert('postoyannoe_ispytanie', [
            'id' => 4,
            'nazvanie' => 'ИК первой категории',
            'pervaya_kategoriya' => false,
            'vysshaya_kategoriya' => true,
        ]);
        $this->insert('postoyannoe_ispytanie', [
            'id' => 5,
            'nazvanie' => 'ИК высшей категории по ФГОС',
            'pervaya_kategoriya' => false,
            'vysshaya_kategoriya' => true,
        ]);
        $this->insert('postoyannoe_ispytanie', [
            'id' => 6,
            'nazvanie' => 'ИК высшей категории',
            'pervaya_kategoriya' => false,
            'vysshaya_kategoriya' => true,
        ]);
    }

    public function safeDown()
    {
        $this->delete('postoyannoe_ispytanie', ['id' => 3]);
        $this->delete('postoyannoe_ispytanie', ['id' => 4]);
        $this->delete('postoyannoe_ispytanie', ['id' => 5]);
        $this->delete('postoyannoe_ispytanie', ['id' => 6]);
    }
}
