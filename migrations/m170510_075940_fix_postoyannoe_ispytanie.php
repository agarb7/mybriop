<?php

use yii\db\Migration;

class m170510_075940_fix_postoyannoe_ispytanie extends Migration
{
    
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->update('postoyannoe_ispytanie',
            ['pervaya_kategoriya' => true,
            'vysshaya_kategoriya' => false],
            ['id' => 4]
        );
        $this->insert('postoyannoe_ispytanie', [
            'id' => 7,
            'nazvanie' => 'Информационная карта',
            'pervaya_kategoriya' => true,
            'vysshaya_kategoriya' => true,
        ]);
    }

    public function safeDown()
    {
        $this->delete('postoyannoe_ispytanie', ['id' => 7]);
    }
}
