<?php

use yii\db\Schema;
use yii\db\Migration;

class m160608_142713_create_function_fot_attestaciya_report extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION get_otraslevoe_soglashenie(zayavlenie_na_attestaciyu_param bigint)
  RETURNS varchar(4096)
AS $$
select string_agg(d.tip_nazvanie || ': ' || d.dostizheniya, ';')
from
  (
    select os.tip_nazvanie, string_agg(os.nazvanie,',') as dostizheniya
    from otraslevoe_soglashenie_zayavleniya as osz
      inner join otraslevoe_soglashenie as os on osz.otraslevoe_soglashenie = os.id
    where osz.zayavlenie_na_attestaciyu = $1
    group by os.tip, os.tip_nazvanie
  ) as d;
$$ LANGUAGE sql;
SQL;
        $this->execute($sql);
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION is_otraslevoe_soglashenie(zayavlenie_na_attestaciyu_param bigint)
RETURNS boolean
AS $$
BEGIN
  if (exists(select * from otraslevoe_soglashenie_zayavleniya where zayavlenie_na_attestaciyu = $1 LIMIT 1)) then
    RETURN true;
  else
    RETURN false;
  end if;
END;
$$ LANGUAGE plpgsql;
SQL;
        $this->execute($sql);
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
    sum(case when bally.ispytanie_tip = 'post' and bally.ispytanie_id = 1 then bally.avg_ball else 0 end) as portfolio,
    sum(case when bally.ispytanie_tip = 'var3' then bally.avg_ball else 0 end) as variativnoe_ispytanie_3,
    sum(case when bally.ispytanie_tip = 'post' and bally.ispytanie_id = 2 then bally.avg_ball else 0 end) as spd
  FROM
  zayavlenie_na_attestaciyu as z
    LEFT JOIN raspredelenie_zayavlenij_na_attestaciyu as r on z.id = r.zayavlenie_na_attestaciyu
    LEFT JOIN rabotnik_attestacionnoj_komissii as ra on r.rabotnik_attestacionnoj_komissii = ra.id
    LEFT JOIN (

    SELECT
      olz.zayavlenie_na_attestaciyu,
      'post'                                                        AS ispytanie_tip,
      olz.postoyannoe_ispytanie                                     AS ispytanie_id,
      cast(sum(solz.bally) AS FLOAT) / count(olz.rabotnik_komissii) AS avg_ball
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
      cast(sum(solz.bally) AS FLOAT) / count(olz.rabotnik_komissii) AS avg_ball
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
      cast(sum(solz.bally) AS FLOAT) / count(olz.rabotnik_komissii) AS avg_ball
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
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION attestaciya_itogovij_otchet(vremya_provedeniya_param bigint)
  RETURNS TABLE (
    id BIGINT,
    vremya_provedeniya BIGINT,
    fio TEXT,
    organizaciya nazvanie,
    dolzhnost nazvanie,
    god_rozhdeniya int,
    imeushayasya_kategoriya kategoriya_ped_rabotnika,
    attestaciya_data_prisvoeniya date,
    na_kategoriyu kategoriya_ped_rabotnika,
    ped_stazh stazh,
    rabota_stazh_v_dolzhnosti stazh,
    stazh_v_dolzhnosti stazh,
    obrazovanie text,
    kursy text,
    kolichestvo_rabotnikov bigint,
    kolichestvo_podpisannih_otsenok bigint,
    portfolio double PRECISION,
    variativnoe_ispytanie_3 double PRECISION,
    spd double PRECISION,
    otraslevoe_soglashenie varchar(4096)
  )
AS $$
BEGIN
 RETURN QUERY
  select
    z.id,
    z.vremya_provedeniya,
    CONCAT(z.familiya,' ' || z.imya, ' ' || z.otchestvo) as fio,
    org.nazvanie as organizaciya,
    d.nazvanie as dolzhnost,
    cast(Extract(YEAR from z.data_rozhdeniya::date) as int) as god_rozhdeniya,
    z.attestaciya_kategoriya as imeushayasya_kategoriya,
    z.attestaciya_data_prisvoeniya,
    z.na_kategoriyu,
    z.ped_stazh,
    z.rabota_stazh_v_dolzhnosti,
    z.stazh_v_dolzhnosti,
    string_agg(
      case when obr.kurs_tip is null then
        CONCAT(
            CASE WHEN gorod.oficialnoe_nazvanie is not null THEN gorod.oficialnoe_nazvanie
                 WHEN rajon.oficialnoe_nazvanie is not null THEN rajon.oficialnoe_nazvanie
                 ELSE ''
            END,
            ', ' || obr_org.nazvanie,
            ', ' || cast(extract(year from obr.dokument_ob_obrazovanii_data) as varchar(4)) || ' г.',
            ', ' || k.nazvanie
        )
      else null end  , ';'
    ) as obrazovanie,
    string_agg(
      case when obr.kurs_tip is not null then
        CONCAT(
          extract(year from obr.dokument_ob_obrazovanii_data),
          ' - ' || obr.kurs_chasy || 'ч.'
        )
      else null end, ';'
    ) as kursy,
    (attestaciya_otsenki(z.id)).*,
    get_otraslevoe_soglashenie(z.id)
  from zayavlenie_na_attestaciyu as z
  inner join obrazovanie_dlya_zayavleniya_na_attestaciyu as obr on z.id = obr.zayavlenie_na_attestaciyu
  left join organizaciya as obr_org on obr.organizaciya = obr_org.id
  left join kvalifikaciya as k on obr.kvalifikaciya = k.id
  left join tip_dokumenta_ob_obrazovanii_view as doc_tip on cast(obr.dokument_ob_obrazovanii_tip as VARCHAR(20)) = doc_tip.nazvanie
  left join adresnyj_objekt as adr_obj on obr_org.adres_adresnyj_objekt = adr_obj.id
  left join adresnyj_objekt as gorod on adr_obj.roditel_urovnya_goroda = gorod.id and gorod.uroven ='gor'
  left join adresnyj_objekt as rajon on adr_obj.roditel_urovnya_goroda = gorod.id and gorod.uroven ='rajon'
  inner join dolzhnost as d on z.rabota_dolzhnost = d.id
  inner join organizaciya as org on z.rabota_organizaciya = org.id
  WHERE z.vremya_provedeniya = $1 and z.status = 'podpisano_otdelom_attestacii'
  group by z.id, z.vremya_provedeniya, z.familiya, z.imya,z.otchestvo,z.data_rozhdeniya,
           org.nazvanie, d.nazvanie, z.attestaciya_kategoriya, z.na_kategoriyu,
           z.ped_stazh, z.stazh_v_dolzhnosti, z.rabota_stazh_v_dolzhnosti;
END;
$$ LANGUAGE plpgsql;
SQL;
        $this->execute($sql);

    }

    public function down()
    {
        echo "m160608_142713_create_function_fot_attestaciya_report cannot be reverted.\n";

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
