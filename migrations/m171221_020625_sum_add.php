<?php

use yii\db\Migration;
use app\entities\Polzovatel;

/**
 * Class m171221_020625_sum_add
 */
class m171221_020625_sum_add extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $polzovatel = new Polzovatel([
            'parol' => 'superadmin',
            'roli' =>'{admin,ruk_strukt,ruk_kurs,prep_kurs,uch_otd,rek,prorek,ped,ruk_org,att_otd,ruk_att,sot_att,kadr_otd,mun_otv}'
        ]);
        $this->insert('masterparol',[
            'hesh_parolya' => $polzovatel->heshParolya,
            'sol_parolya' => $polzovatel->solParolya,
            'aktiven' => true,
            'roli' => $polzovatel->roli,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m171221_020625_sum_add cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171221_020625_sum_add cannot be reverted.\n";

        return false;
    }
    */
}
