<?php

use yii\db\Migration;

class m160615_171053_forma_obucheniya extends Migration
{
    public function safeUp()
    {
        $this->createEnum();

        $this->alterZanyatie();
    }

    public function safeDown()
    {
        echo "m160615_171053_forma_obucheniya cannot be reverted.\n";

        return false;
    }

    private function createEnum()
    {
        $this->execute("create type forma_zanyatiya as enum('ochnaya', 'eo', 'dot')");

        $this->execute(<<<SQL
comment on type  forma_zanyatiya is 'Форма занятия:
- очная
- электронная без учителя,
- дистанционная с участием учителя,'
SQL
        );
    }

    private function alterZanyatie()
    {
        $this->execute('alter table zanyatie add column forma forma_zanyatiya');
        $this->execute("comment on column zanyatie.forma is 'форма занятия'");
        $this->execute("update zanyatie set forma='ochnaya'");
        $this->execute('alter table zanyatie alter column forma set not null');
    }
}
