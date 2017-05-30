<?php

use yii\db\Migration;

class m170530_065051_change_view_sotrudniki_briop extends Migration
{
    public function up()
    {
        $this->execute('DROP VIEW sotrudniki_briop');
        $this->execute('DROP VIEW vse_sotrudniki_briop');
        $sql = <<<SQL
CREATE VIEW rabotajushhie_sotrudniki_briop
AS SELECT fiz_lico.familiya||' '||fiz_lico.imya||' '||fiz_lico.otchestvo AS fio
    ,fiz_lico.id AS fl_id
    ,polzovatel.login AS Логин
    ,polzovatel.id AS polzovatel_id
    ,strukturnoe_podrazdelenie.nazvanie AS podrazdelenie
    ,strukturnoe_podrazdelenie.id AS podrazdelenie_id
    ,dolzhnost_fiz_lica_na_rabote.id AS dolzhnost_fl_na_r_id 
    ,dolzhnost.nazvanie AS Должность
    ,dolzhnost.tip AS dolzhnost_tip
    ,dolzhnost.id AS dolzhnost_id
FROM rabota_fiz_lica
LEFT JOIN fiz_lico ON fiz_lico.id = rabota_fiz_lica.fiz_lico
LEFT JOIN polzovatel ON polzovatel.fiz_lico = fiz_lico.id
LEFT JOIN organizaciya ON rabota_fiz_lica.organizaciya = organizaciya.id
LEFT JOIN dolzhnost_fiz_lica_na_rabote ON rabota_fiz_lica.id = dolzhnost_fiz_lica_na_rabote.rabota_fiz_lica
LEFT JOIN dolzhnost ON dolzhnost_fiz_lica_na_rabote.dolzhnost = dolzhnost.id
LEFT JOIN strukturnoe_podrazdelenie ON dolzhnost_fiz_lica_na_rabote.strukturnoe_podrazdelenie = strukturnoe_podrazdelenie.id
WHERE organizaciya.id = 1 AND (dolzhnost_fiz_lica_na_rabote.actual = TRUE OR dolzhnost_fiz_lica_na_rabote.actual IS NULL) 
ORDER BY strukturnoe_podrazdelenie.id
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m170530_065051_change_view_sotrudniki_briop cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
