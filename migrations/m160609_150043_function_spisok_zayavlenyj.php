<?php

use yii\db\Schema;
use yii\db\Migration;

class m160609_150043_function_spisok_zayavlenyj extends Migration
{
    public function up()
    {
        $this->execute('DROP FUNCTION IF EXISTS spisok_zayavlenij_dlye_sotrudnika(int,int);');
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION spisok_zayavlenij_dlye_sotrudnika(rabotnik_fiz_lico int, vremya_param int DEFAULT NULL)
RETURNS TABLE
(
  id bigint,
  familiya imya_cheloveka,
  imya imya_cheloveka,
  otchestvo imya_cheloveka,
  organizaciya_id BIGINT,
  organizaciya_nazvanie nazvanie,
  dolzhnost_id BIGINT,
  dolzhnost_nazvanie nazvanie,
  vremya_provedeniya bigint,
  rabotnik_komissii_fiz_lico bigint,
  listy_kolichestvo bigint,
  zapolnennye_list_kolichestvo bigint
)
AS $$
BEGIN
  RETURN QUERY
    select
      z.id,
      z.familiya,
      z.imya,
      z.otchestvo,
      o.id as organizaciya_id,
      o.nazvanie as organizaciya_nazvanie,
      d.id as dolzhnost_id,
      d.nazvanie as dolzhnost_nazvanie,
      z.vremya_provedeniya,
      rak.fiz_lico as rabotnik_komissii_fiz_lico,
      ol.listy_kolichestvo,
      ol.zapolnennye_list_kolichestvo
    from zayavlenie_na_attestaciyu as z
    inner join organizaciya as o on z.rabota_organizaciya = o.id
    inner join dolzhnost as d on z.rabota_dolzhnost = d.id
    inner join raspredelenie_zayavlenij_na_attestaciyu as rzna on z.id = rzna.zayavlenie_na_attestaciyu
    inner join rabotnik_attestacionnoj_komissii as rak on rzna.rabotnik_attestacionnoj_komissii = rak.id
    left join
    (
      select
        rabotnik_komissii,
        zayavlenie_na_attestaciyu,
        count(1) as listy_kolichestvo,
        sum(case when status = 'zapolneno' then 1 else 0 end) zapolnennye_list_kolichestvo
      from otsenochnyj_list_zayavleniya
      GROUP BY rabotnik_komissii,zayavlenie_na_attestaciyu
    ) as ol on rak.fiz_lico = ol.rabotnik_komissii and z.id = ol.zayavlenie_na_attestaciyu
    where rak.fiz_lico = $1 and
          (
            case when $2 is null
              then 1
              else
                case when z.vremya_provedeniya = $2
                  then 1
                  else 0
                end
            end
          ) = 1;
END
$$ LANGUAGE plpgsql;
SQL;
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160609_150043_function_spisok_zayavlenyj cannot be reverted.\n";

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
