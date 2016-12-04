<?php
namespace app\records;

use app\base\ActiveQuery;
use app\base\ActiveRecord;

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
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fiz_lico';
    }

    /**
     * @return ActiveQuery
     */
    public function getRaboty_fiz_lica_rel()
    {
        return $this
            ->hasMany(RabotaFizLica::className(), ['fiz_lico' => 'id'])
            ->inverseOf('fiz_lico_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getPolzovateli_rel()
    {
        return $this
            ->hasMany(Polzovatel::className(), ['fiz_lico' => 'id'])
            ->inverseOf('fiz_lico_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getKursy_fiz_lica_rel()
    {
        return $this
            ->hasMany(KursFizLica::className(), ['fiz_lico' => 'id'])
            ->inverseOf('fiz_lico_rel');
    }

    public function getFio($short = false){
        $fio = [];
        if ($short){
            if ($this->familiya)
                $fio[] = $this->familiya;

            if ($this->imya) {
                $fio[] = mb_strtoupper(mb_substr($this->imya,0,1)).'.';

                if ($this->otchestvo)
                    $fio[] = mb_strtoupper(mb_substr($this->otchestvo,0,1)).'.';
            }
        }
        else{
            if ($this->familiya)
                $fio[] = $this->familiya;

            if ($this->imya) {
                $fio[] = $this->imya;

                if ($this->otchestvo)
                    $fio[] = $this->otchestvo;
            }
        }
        return implode(' ', $fio);
    }
}