<?php
namespace app\records;

use yii\db\ActiveRecord;



/**
 * FizLico record
 * @property int $id
 * @property string $familiya
 * @property string $imya
 * @property string $otchestvo
 * @property string $data_rozhdeniya
 * @property string $pasport_no
 * @property string $pasport_kem_vydan_kod
 * @property string $pasport_kem_vydan
 * @property string $pasport_kogda_vydan
 * @property string $inn
 * @property string $snils
 * @property string $telefon
 * @property string $email
 * @property int $propiska_adresnyj_objekt bigint,
 * @property string $propiska_dom
 * @property string $propiska_kvartira
 * @property int $ped_stazh stazh,
 * @property int $kopiya_trudovoj_knizhki
 * @property string $propiska
 */
class FizLico extends ActiveRecord
{
    public static function tableName()
    {
        return 'fiz_lico';
    }

    public function getRaboty_fiz_lica_rel()
    {
        return $this
            ->hasMany(RabotaFizLica::className(), ['fiz_lico' => 'id'])
            ->inverseOf('fiz_lico_rel');
    }
}