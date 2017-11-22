<?php

use yii\db\Migration;

/**
 * Class m171122_024544_update_dok_roli_rektor
 */
class m171122_024544_update_dok_roli_rektor extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('dok_roli',['polzovatel_roli' => '{rek}', 'dolzhnost_id' => null], 'id=5');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('dok_roli',['polzovatel_roli' => null, 'dolzhnost_id' => 41], 'id=5');
    }
}
