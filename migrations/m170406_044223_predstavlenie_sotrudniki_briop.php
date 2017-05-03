<?php

use yii\db\Migration;

class m170406_044223_predstavlenie_sotrudniki_briop extends Migration
{
    public function up()
    {
$sql = <<<SQL
CREATE VIEW vse_sotrudniki_briop
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
WHERE organizaciya.id = 1
ORDER BY strukturnoe_podrazdelenie.id
SQL;
        $this->execute($sql);
    }

    public function down()
    {
$sql = <<<SQL
DROP VIEW vse_sotrudniki_briop
SQL;
        $this->execute($sql);
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