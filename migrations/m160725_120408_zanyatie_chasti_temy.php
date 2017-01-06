<?php
use yii\db\Migration;

class m160725_120408_zanyatie_chasti_temy extends Migration
{
    public function safeUp()
    {
        $this->createZanyatieChastiTemy();

        $this->insertZanyatiyaChastejTem();
        $this->alterZanyatie();
    }

    public function safeDown()
    {
        echo "m160725_120408_potok cannot be reverted.\n";

        return false;
    }

    //todo add constraint trigger for unique kurs in one zanyatie
    public function createZanyatieChastiTemy()
    {
        $sql = <<<SQL
create table zanyatie_chasti_temy (
  tema bigint references tema(id),
  chast_temy integer,
  zanyatie bigint references zanyatie(id) not null,
  primary key (tema, chast_temy),  
  check (chast_temy >= 1 and chast_temy <= 100)
)
SQL;

        $this->execute($sql);
        $this->execute("comment on table zanyatie_chasti_temy is 'какому занятию принадлежит часть темы'");
        $this->execute("comment on column zanyatie_chasti_temy.chast_temy is 'номер двухчасовой части темы'");
    }

    public function insertZanyatiyaChastejTem()
    {
        $sql = <<<SQL
insert into zanyatie_chasti_temy 
select tema, chast_temy, id from zanyatie;
SQL;

        $this->execute($sql);
    }

    //todo add constraint trigger for unique data, nomer, kurs
    public function alterZanyatie()
    {
        $sqlAlter = <<<SQL
alter table zanyatie        
  drop column kurs,
  drop column tema,
  drop column chast_temy,
  add column nazvanie nazvanie,
  alter column data drop not null,
  alter column nomer drop not null,
  add unique (data, nomer, auditoriya),
  add unique (data, nomer, prepodavatel)
SQL;

        $this->execute($sqlAlter);
        $this->execute("comment on column zanyatie.nazvanie is 'назначенное обобщённое название темы для занятия'");
    }
}
