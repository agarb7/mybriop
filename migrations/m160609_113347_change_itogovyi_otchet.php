<?php

use yii\db\Schema;
use yii\db\Migration;

class m160609_113347_change_itogovyi_otchet extends Migration
{
    public function up()
    {
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
        echo "m160609_113347_change_itogovyi_otchet cannot be reverted.\n";

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
