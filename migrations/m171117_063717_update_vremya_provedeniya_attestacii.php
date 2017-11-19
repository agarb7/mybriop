<?php

use yii\db\Migration;

/**
 * Class m171117_063717_update_vremya_provedeniya_attestacii
 */
class m171117_063717_update_vremya_provedeniya_attestacii extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2017-10-20',
            'priem_zayavleniya_konec' => '2017-11-20',
            'nachalo' => '2018-01-01',
            'konec' => '2018-01-31'
        ],'id=23');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2017-11-21',
            'priem_zayavleniya_konec' => '2017-12-19',
            'nachalo' => '2018-02-01',
            'konec' => '2018-02-28'
        ],'id=24');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2017-12-20',
            'priem_zayavleniya_konec' => '2018-01-20',
            'nachalo' => '2018-03-01',
            'konec' => '2018-03-31'
        ],'id=25');

        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-02-21',
            'priem_zayavleniya_konec' => '2018-03-19',
            'nachalo' => '2018-04-01',
            'konec' => '2018-04-30'
        ],'id=26');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-03-20',
            'priem_zayavleniya_konec' => '2018-04-20',
            'nachalo' => '2018-05-01',
            'konec' => '2018-05-31'
        ],'id=27');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171117_063717_update_vremya_provedeniya_attestacii cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171117_063717_update_vremya_provedeniya_attestacii cannot be reverted.\n";

        return false;
    }
    */
}
