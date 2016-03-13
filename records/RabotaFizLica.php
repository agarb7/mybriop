<?php
namespace app\records;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * RabotaFizLica record
 *
 * @property int id
 * @property int fiz_lico
 * @property int organizaciya
 * @property string org_tip
 * @property float dolya_stavki
 * @property string telefon
 */
class RabotaFizLica extends ActiveRecord
{
    public static function tableName()
    {
        return 'rabota_fiz_lica';
    }

    /**
     * @return ActiveQuery
     */
    public function getFiz_lico_rel()
    {
        return $this
            ->hasOne(FizLico::className(), ['id' => 'fiz_lico'])
            ->inverseOf('raboty_fiz_lica_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getDolzhnosti_fiz_lica_na_rabote_rel()
    {
        return $this
            ->hasMany(DolzhnostFizLicaNaRabote::className(), ['rabota_fiz_lica' => 'id'])
            ->inverseOf('rabota_fiz_lica_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getOrganizaciya_rel()
    {
        return $this
            ->hasOne(Organizaciya::className(), ['id' => 'organizaciya']);
    }
}