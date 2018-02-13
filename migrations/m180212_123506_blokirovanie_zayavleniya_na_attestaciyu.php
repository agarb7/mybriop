<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Class m180212_123506_blokirovanie_zayavleniya_na_attestaciyu
 */
class m180212_123506_blokirovanie_zayavleniya_na_attestaciyu extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('lock_zayavleniya_na_attestaciyu',[
            'id' => Schema::TYPE_PK,
            'nazvanie' => Schema::TYPE_STRING.' NOT NULL ',
            'text' => 'squeezed_text NOT NULL'
        ]);

        $this->insert('lock_zayavleniya_na_attestaciyu',[
            'nazvanie'=>'Не предоставлены материалы в срок',
            'text' => \app\globals\ApiGlobals::to_trimmed_text('В связи с тем, что Вами не были предоставлены аттестационные материалы в установленные сроки. Если Вы планируете проходить аттестацию в дальнейшем, Вам необходимо заново подать заявление.')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180212_123506_blokirovanie_zayavleniya_na_attestaciyu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180212_123506_blokirovanie_zayavleniya_na_attestaciyu cannot be reverted.\n";

        return false;
    }
    */
}
