<?php

use yii\db\Migration;

/**
 * Class m171122_072739_update2_vremya_provedeniya_attestacii
 */
class m171122_072739_update2_vremya_provedeniya_attestacii extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-01-21',
            'priem_zayavleniya_konec' => '2018-02-19',
            'nachalo' => '2018-04-01',
            'konec' => '2018-04-30'
        ],'id=26');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-02-20',
            'priem_zayavleniya_konec' => '2018-03-20',
            'nachalo' => '2018-05-01',
            'konec' => '2018-05-31'
        ],'id=27');
        
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-06-20',
            'priem_zayavleniya_konec' => '2018-07-20',
            'nachalo' => '2018-09-01',
            'konec' => '2018-09-30'
        ],'id=28');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-07-21',
            'priem_zayavleniya_konec' => '2018-08-19',
            'nachalo' => '2018-10-01',
            'konec' => '2018-10-31'
        ],'id=29');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-08-20',
            'priem_zayavleniya_konec' => '2018-09-20',
            'nachalo' => '2018-11-01',
            'konec' => '2018-11-30'
        ],'id=30');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-09-21',
            'priem_zayavleniya_konec' => '2018-10-19',
            'nachalo' => '2018-12-01',
            'konec' => '2018-12-31'
        ],'id=31');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-10-20',
            'priem_zayavleniya_konec' => '2018-11-20',
            'nachalo' => '2019-01-01',
            'konec' => '2019-01-31'
        ],'id=32');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-11-21',
            'priem_zayavleniya_konec' => '2018-12-19',
            'nachalo' => '2019-02-01',
            'konec' => '2019-02-28'
        ],'id=33');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2018-12-20',
            'priem_zayavleniya_konec' => '2019-01-20',
            'nachalo' => '2019-03-01',
            'konec' => '2019-03-31'
        ],'id=34');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2019-01-21',
            'priem_zayavleniya_konec' => '2019-02-19',
            'nachalo' => '2019-04-01',
            'konec' => '2019-04-30'
        ],'id=35');
        $this->update('vremya_provedeniya_attestacii', [
            'priem_zayavleniya_nachalo' => '2019-02-20',
            'priem_zayavleniya_konec' => '2019-03-20',
            'nachalo' => '2019-05-01',
            'konec' => '2019-05-31'
        ],'id=36');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171122_072739_update2_vremya_provedeniya_attestacii cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171122_072739_update2_vremya_provedeniya_attestacii cannot be reverted.\n";

        return false;
    }
    */
}
