<?php

use yii\db\Migration;

class m161105_082114_function_count_bally_below_min extends Migration
{
    public function up()
    {
        $this->execute('DROP FUNCTION IF EXISTS count_bally_below_min(BIGINT);');
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION count_bally_below_min(zayavlenie_id BIGINT)
RETURNS TABLE
(
  count_below BIGINT
)
AS $$
BEGIN
RETURN QUERY
SELECT
  COALESCE(sum(case when t.min_bally > t.bally then 1 else 0 end),0) as count
FROM
  (
    SELECT
      olz.id,
      CASE
      WHEN z.na_kategoriyu = 'pervaya_kategoriya'
        THEN olz.min_ball_pervaya_kategoriya
      ELSE olz.min_ball_visshaya_kategoriya
      END             AS min_bally,
      sum(solz.bally) AS bally
    FROM zayavlenie_na_attestaciyu AS z
      INNER JOIN otsenochnyj_list_zayavleniya AS olz ON z.id = olz.zayavlenie_na_attestaciyu
      INNER JOIN struktura_otsenochnogo_lista_zayvaleniya AS solz ON olz.id = solz.otsenochnyj_list_zayavleniya
      INNER JOIN (
                   SELECT
                     rzna.zayavlenie_na_attestaciyu,
                     rzna.status,
                     rak.fiz_lico AS rabotnik_komissii
                   FROM raspredelenie_zayavlenij_na_attestaciyu AS rzna
                     INNER JOIN rabotnik_attestacionnoj_komissii AS rak
                       ON rzna.rabotnik_attestacionnoj_komissii = rak.id
                   WHERE rzna.status = 'podpisano'
                 ) AS rabotnik
        ON olz.rabotnik_komissii = rabotnik.rabotnik_komissii AND z.id = rabotnik.zayavlenie_na_attestaciyu
    WHERE z.id = $1 AND solz.uroven = 1 AND rabotnik.status = 'podpisano'
    GROUP BY olz.id, z.na_kategoriyu, olz.min_ball_visshaya_kategoriya, olz.min_ball_pervaya_kategoriya
  ) as t;
END
$$ LANGUAGE plpgsql;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m161105_082114_function_count_bally_below_min cannot be reverted.\n";

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
