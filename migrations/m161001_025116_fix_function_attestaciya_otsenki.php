<?php

use yii\db\Schema;
use yii\db\Migration;

class m161001_025116_fix_function_attestaciya_otsenki extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION attestaciya_otsenki(zayavlenie_na_attestaciyu_id bigint)
RETURNS TABLE
(
  kolichestvo_rabotnikov bigint,
  kolichestvo_podpisannih_otsenok bigint,
  portfolio double PRECISION,
  variativnoe_ispytanie_3 double PRECISION,
  spd double PRECISION
)
AS $$
BEGIN
  RETURN QUERY
  select
    count(r.id) as kolichestvo_rabotnikov,
    COALESCE (SUM(case when r.status = 'podpisano' then 1 else 0 end),0) as kolichestvo_podpisannih_otsenok,
    max(case when bally.ispytanie_tip = 'post' and bally.ispytanie_id = 1 then bally.avg_ball else 0 end) as portfolio,
    max(case when bally.ispytanie_tip = 'var3' then bally.avg_ball else 0 end) as variativnoe_ispytanie_3,
    max(case when bally.ispytanie_tip = 'post' and bally.ispytanie_id = 2 then bally.avg_ball else 0 end) as spd
  FROM
  zayavlenie_na_attestaciyu as z
    LEFT JOIN raspredelenie_zayavlenij_na_attestaciyu as r on z.id = r.zayavlenie_na_attestaciyu
    LEFT JOIN rabotnik_attestacionnoj_komissii as ra on r.rabotnik_attestacionnoj_komissii = ra.id
    LEFT JOIN (

    SELECT
      olz.zayavlenie_na_attestaciyu,
      'post'                                                        AS ispytanie_tip,
      olz.postoyannoe_ispytanie                                     AS ispytanie_id,
      cast(sum(solz.bally) AS FLOAT) / count(distinct olz.rabotnik_komissii) AS avg_ball
    FROM zayavlenie_na_attestaciyu as z
      INNER JOIN otsenochnyj_list_zayavleniya AS olz on z.id = olz.zayavlenie_na_attestaciyu
      INNER JOIN struktura_otsenochnogo_lista_zayvaleniya AS solz ON olz.id = solz.otsenochnyj_list_zayavleniya
      INNER JOIN(
        SELECT rzna.zayavlenie_na_attestaciyu, rzna.status, rak.fiz_lico as rabotnik_komissii
        FROM raspredelenie_zayavlenij_na_attestaciyu as rzna
        INNER JOIN rabotnik_attestacionnoj_komissii as rak on rzna.rabotnik_attestacionnoj_komissii = rak.id
        WHERE rzna.status = 'podpisano'
      ) as rabotnik on olz.rabotnik_komissii = rabotnik.rabotnik_komissii and z.id = rabotnik.zayavlenie_na_attestaciyu
    WHERE olz.zayavlenie_na_attestaciyu = $1 AND
          olz.postoyannoe_ispytanie = 1 AND
          solz.uroven = 1
    GROUP BY olz.zayavlenie_na_attestaciyu, olz.postoyannoe_ispytanie
    UNION
    SELECT
      olz.zayavlenie_na_attestaciyu,
      'var3'                                                        AS ispytanie_tip,
      olz.var_ispytanie_3                                           AS ispytanie_id,
      cast(sum(solz.bally) AS FLOAT) / count(distinct olz.rabotnik_komissii) AS avg_ball
    FROM zayavlenie_na_attestaciyu as z
      INNER JOIN otsenochnyj_list_zayavleniya AS olz on z.id = olz.zayavlenie_na_attestaciyu
      INNER JOIN struktura_otsenochnogo_lista_zayvaleniya AS solz ON olz.id = solz.otsenochnyj_list_zayavleniya
      INNER JOIN(
                  SELECT rzna.zayavlenie_na_attestaciyu, rzna.status, rak.fiz_lico as rabotnik_komissii
                  FROM raspredelenie_zayavlenij_na_attestaciyu as rzna
                    INNER JOIN rabotnik_attestacionnoj_komissii as rak on rzna.rabotnik_attestacionnoj_komissii = rak.id
                  WHERE rzna.status = 'podpisano'
                ) as rabotnik on olz.rabotnik_komissii = rabotnik.rabotnik_komissii and z.id = rabotnik.zayavlenie_na_attestaciyu
    WHERE olz.zayavlenie_na_attestaciyu = $1 AND
          olz.var_ispytanie_3 IS NOT NULL AND
          solz.uroven = 1
    GROUP BY olz.zayavlenie_na_attestaciyu, olz.var_ispytanie_3
    UNION
    SELECT
      olz.zayavlenie_na_attestaciyu,
      'post'                                                        AS ispytanie_tip,
      olz.postoyannoe_ispytanie                                     AS ispytanie_id,
      cast(sum(solz.bally) AS FLOAT) / count(distinct olz.rabotnik_komissii) AS avg_ball
    FROM zayavlenie_na_attestaciyu as z
      INNER JOIN otsenochnyj_list_zayavleniya AS olz on z.id = olz.zayavlenie_na_attestaciyu
      INNER JOIN struktura_otsenochnogo_lista_zayvaleniya AS solz ON olz.id = solz.otsenochnyj_list_zayavleniya
      INNER JOIN(
                  SELECT rzna.zayavlenie_na_attestaciyu, rzna.status, rak.fiz_lico as rabotnik_komissii
                  FROM raspredelenie_zayavlenij_na_attestaciyu as rzna
                    INNER JOIN rabotnik_attestacionnoj_komissii as rak on rzna.rabotnik_attestacionnoj_komissii = rak.id
                  WHERE rzna.status = 'podpisano'
                ) as rabotnik on olz.rabotnik_komissii = rabotnik.rabotnik_komissii and z.id = rabotnik.zayavlenie_na_attestaciyu
    WHERE olz.zayavlenie_na_attestaciyu = $1 AND
          olz.postoyannoe_ispytanie = 2 AND
          solz.uroven = 1
    GROUP BY olz.zayavlenie_na_attestaciyu, olz.postoyannoe_ispytanie
  ) as bally on z.id = bally.zayavlenie_na_attestaciyu
  where z.id = $1
  group by z.id;
END
$$  LANGUAGE plpgsql;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m161001_025116_fix_function_attestaciya_otsenki cannot be reverted.\n";

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
