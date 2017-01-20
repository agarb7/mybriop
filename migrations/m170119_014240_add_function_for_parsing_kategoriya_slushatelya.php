<?php

use yii\db\Migration;

class m170119_014240_add_function_for_parsing_kategoriya_slushatelya extends Migration
{
    public function up()
    {
$sql = <<<SQL
CREATE OR REPLACE FUNCTION parsing_kategoriya_slushatelya() RETURNS void AS 
$$
DECLARE 
kategoriya text;
id_kategoriya bigint;
r kurs%rowtype;
row_count bigint;
BEGIN
  FOR r IN SELECT * FROM kurs WHERE plan_prospekt_god = '2017-01-01' and kategoriya_slushatelya is not null
  LOOP
    raise notice 'ID: %, Группа категорий: % ',r.id, string_to_array(r.kategoriya_slushatelya, ',');
    foreach kategoriya in array string_to_array(r.kategoriya_slushatelya, ',')
    loop
      kategoriya:=TRIM(' '  FROM  kategoriya);
      id_kategoriya:=(select id from kategoriya_slushatelya where nazvanie = kategoriya);
      if (id_kategoriya is not null) 
      then 
        raise notice '%, %, %', r.id, kategoriya, id_kategoriya; 
        row_count:=(select count(*) from kategoriya_slushatelya_kursa where kurs=r.id and kategoriya_slushatelya=id_kategoriya);
        if (row_count=0) 
        then
          INSERT INTO kategoriya_slushatelya_kursa (kurs, kategoriya_slushatelya) VALUES (r.id, id_kategoriya);
          raise notice 'Категория % для курса % успешно добавлена!', id_kategoriya, r.id;
        else raise notice 'Число совпадений для записи: %. Данные не нуждаются в добавлении!', row_count; 
        end if;  
      else 
        raise notice 'Категория: % будет добавлена!', kategoriya;
        INSERT INTO kategoriya_slushatelya (nazvanie) VALUES (kategoriya);
        id_kategoriya:=(select id from kategoriya_slushatelya where nazvanie = kategoriya);
        raise notice 'ID добавленной категории: %', id_kategoriya;
        INSERT INTO kategoriya_slushatelya_kursa (kurs, kategoriya_slushatelya) VALUES (r.id, id_kategoriya);
      end if;
    end loop;
  END LOOP;
END
$$
LANGUAGE plpgsql;
SQL;
        
        $this->execute($sql);
    }

    public function down()
    {
        echo "m170119_014240_add_function_for_parsing_kategoriya_slushatelya cannot be reverted.\n";

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
