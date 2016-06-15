<?php
namespace app\records;

use app\base\ActiveQuery;
use app\base\ActiveRecord;

/**
 * DolzhnostFizLicaNaRabote record
 *
 * @property integer $id
 * @property integer $rabota_fiz_lica
 * @property integer $strukturnoe_podrazdelenie
 * @property boolean $rukovoditel_strukturnogo_podrazdeleniya
 * @property integer $dolzhnost
 * @property string $org_tip
 * @property string $etap_obrazovaniya
 * @property integer $stazh
 */
class DolzhnostFizLicaNaRabote extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dolzhnost_fiz_lica_na_rabote';
    }

    /**
     * @return ActiveQuery
     */
    public function getRabota_fiz_lica_rel()
    {
        return $this
            ->hasOne(RabotaFizLica::className(), ['id' => 'rabota_fiz_lica'])
            ->inverseOf('dolzhnosti_fiz_lica_na_rabote_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getDolzhnost_rel()
    {
        return $this
            ->hasOne(Dolzhnost::className(), ['id' => 'dolzhnost']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStrukturnoe_podrazdelenie_rel()
    {
        return $this
            ->hasOne(StrukturnoePodrazdelenie::className(), ['id' => 'strukturnoe_podrazdelenie']);
    }
}