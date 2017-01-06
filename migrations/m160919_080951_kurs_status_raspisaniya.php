<?php

use yii\db\Migration;

class m160919_080951_kurs_status_raspisaniya extends Migration
{
    public function safeUp()
    {
        $this->createStatusRaspisaniyaEnum();
        $this->addColumnStatusRaspisaniya();
        $this->updateStatusRaspisaniya();
    }

    public function safeDown()
    {
        echo "m160919_080951_kurs_status_raspisaniya cannot be reverted.\n";

        return false;
    }


    private function createStatusRaspisaniyaEnum()
    {
        $create = <<<SQL
create type status_raspisaniya_kursa as enum (  
  'redaktiruetsya',
  'zaversheno'
) 
SQL;

        $comment = <<<SQL
comment on type status_raspisaniya_kursa is 
'Статус расписания курса:
- редактируется, - разрешено редактировать: создаётся или правится, черновой вариант;
- завершена, - окончательные правки сделаны, расписание считается завершённым, чистовой вариант.'
SQL;

        $this->execute($create);
        $this->execute($comment);
    }

    private function addColumnStatusRaspisaniya()
    {
        $alter = <<<SQL
alter table kurs 
  add column status_raspisaniya status_raspisaniya_kursa,
  add check (status_raspisaniya is null or status_programmy = 'zavershena')
SQL;

        $comment = <<<SQL
comment on column kurs.status_raspisaniya is
'На каком этапе составление расписания. Расписание может редактироваться, после того как программа завершена'
SQL;

        $this->execute($alter);
        $this->execute($comment);
    }

    private function updateStatusRaspisaniya()
    {
        $sql = <<<SQL
update kurs k set 
  status_programmy = 'zavershena'::status_programmy_kursa, 
  status_raspisaniya = 'redaktiruetsya'::status_raspisaniya_kursa 
from zanyatie z
left join zanyatie_chasti_temy zct on zct.zanyatie = z.id
left join tema t on t.id = zct.tema  
left join podrazdel_kursa p on p.id = t.podrazdel
left join razdel_kursa r on r.id = p.razdel
where r.kurs = k.id
SQL;

        $this->execute($sql);
    }
}
