<?php

use yii\db\Migration;

class m160408_142436_raspisanie extends Migration
{
    public function safeUp()
    {
        $this->createAuditoriya();
        $this->initAuditoriya();

        $this->alterKurs();
        $this->createZanyatie();
    }

    public function safeDown()
    {
        echo "m160408_142436_auditoriya cannot be reverted.\n";

        return false;
    }

    private function createAuditoriya()
    {
        $creationSql = <<<SQL
create table auditoriya (
  id bigserial primary key,
  nazvanie nazvanie not null,
  obschij boolean not null
)
SQL;

        $this->execute($creationSql);
        $this->execute("comment on table auditoriya is 'Справочник аудиторий'");
    }

    private function initAuditoriya()
    {
        $auditorii = ['13', '14', '16', '22', '25', '26', '27', 'Акт. зал', 'Конф. зал'];

        $rows = array_map(function ($val) {
            return [$val, true];
        }, $auditorii);

        $this->batchInsert('auditoriya', ['nazvanie', 'obschij'], $rows);
    }

    private function alterKurs()
    {
        $alterSql = <<<SQL
alter table kurs
  add column raspisanie_nachalo date,
  add column raspisanie_konec date,
  add column auditoriya_po_umolchaniyu bigint references auditoriya(id);
SQL;

        $this->execute($alterSql);

        $this->createColumnComments('kurs', [
            'raspisanie_nachalo' => 'начало занятий по расписанию',
            'raspisanie_konec' => 'конец занятий по расписанию',
            'auditoriya_po_umolchaniyu' => 'аудитория по умолчанию при составлении расписания'
        ]);
    }


    private function createZanyatie()
    {
        $creationSql = <<<SQL
create table zanyatie (
  id bigserial primary key,
  kurs bigint not null references kurs(id),
  tema bigint not null references tema(id),
  chast_temy integer not null check (chast_temy between 1 and 100),
  data date not null,
  nomer integer not null check (nomer between 1 and 6),  
  prepodavatel bigint references fiz_lico(id),
  auditoriya bigint references auditoriya(id),
  unique (kurs, data, nomer),
  unique (tema, chast_temy)
)
SQL;

        $this->execute($creationSql);
        $this->execute("comment on table zanyatie is 'Занятие в расписании курса'");

        $this->createColumnComments('zanyatie', [
            'chast_temy' => 'номер двухчасовой части темы',
            'data' => 'дата проведения',
            'nomer' => 'время проведения (номер в сетке расписания дня)'
        ]);
    }

    private function createColumnComments($table, $comments)
    {
        foreach ($comments as $column => $comment) {
            $qComment = $this->db->quoteValue($comment);
            $this->execute("comment on column $table.$column is $qComment");
        }
    }
}
