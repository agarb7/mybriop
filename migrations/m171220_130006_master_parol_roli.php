<?php

use yii\db\Migration;
use app\entities\Polzovatel;
use yii\db\mysql\Schema;

/**
 * Class m171220_130006_master_parol_roli
 */
class m171220_130006_master_parol_roli extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('masterparol', 'roli', 'roli');
        $this->dropColumn('masterparol', 'id');
        $this->addColumn('masterparol','id',Schema::TYPE_PK);

        $this->update('masterparol',['aktiven' => false],'id=1');
        
        $polzovatel = new Polzovatel([
            'parol' => 'masteratt',
            'roli' =>'{ruk_strukt,ruk_kurs,prep_kurs,uch_otd,ped,ruk_org,att_otd,ruk_att,sot_att,kadr_otd,mun_otv}'
        ]);
        $this->insert('masterparol',[
            'hesh_parolya' => $polzovatel->heshParolya,
            'sol_parolya' => $polzovatel->solParolya,
            'aktiven' => true,
            'roli' => $polzovatel->roli,
        ]);

        $polzovatel = new Polzovatel([
            'parol' => 'uchmaster',
            'roli' =>'{ruk_strukt,ruk_kurs,prep_kurs,uch_otd,prorek,ped,ruk_org,ruk_att,sot_att,kadr_otd,mun_otv}'
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
        echo "m171220_130006_master_parol_roli cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171220_130006_master_parol_roli cannot be reverted.\n";

        return false;
    }
    */
}
