<?php

use yii\db\Migration;

class m160301_113432_kurs_fiz_lica_iup extends Migration
{
    public function up()
    {
        $this->addIupFlag();
        $this->alterStatusKursaFizLica();
    }

    public function down()
    {
        echo "m160301_113432_kurs_fiz_lica_status_iup cannot be reverted.\n";

        return false;
    }

    private function alterStatusKursaFizLica()
    {
        $this->execute("alter table kurs_fiz_lica add column status_bak varchar");
        $this->execute("update kurs_fiz_lica set status_bak = status");
        $this->execute("alter table kurs_fiz_lica drop column status");

        $this->execute("drop type status_kursa_fiz_lica");
        $this->execute("create type status_kursa_fiz_lica as enum ('ozhid', 'zap', 'otm_slush', 'otm_briop', 'projd')");

        $comment = <<<TEXT
Статус записи курса физ лица
- запись ожидает подтверждения (ozhid),
- записан на курс (zap),
- запись на курс была отменена слушателем (otm_slush),
- запись на курс была отменена БРИОП (otm_briop),
- курс успешно пройден (projd)
TEXT;

        $this->execute("comment on type status_kursa_fiz_lica is '$comment'");

        $this->execute("alter table kurs_fiz_lica add column status status_kursa_fiz_lica");
        $case = "case status_bak when 'otm' then 'otm_slush' when 'otm_ruk' then 'otm_briop' else status_bak::status_kursa_fiz_lica end";
        $this->execute("update kurs_fiz_lica set status = $case");
        $this->execute("alter table kurs_fiz_lica alter column status set not null");
        $this->execute("alter table kurs_fiz_lica drop column status_bak");
    }

    private function addIupFlag()
    {
        $this->execute("alter table kurs_fiz_lica add column iup boolean default false not null");
    }
}
