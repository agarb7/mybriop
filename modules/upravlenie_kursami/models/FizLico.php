<?php
namespace app\upravlenie_kursami\models;

use app\records\StrukturnoePodrazdelenie;
use yii\helpers\ArrayHelper;

use app\base\ActiveQuery;
use app\helpers\SqlType;

use app\enums2\Rol;

use app\records\Organizaciya;


/** 
 * @property RabotaFizLica $pervaya_rabota_fiz_lica_v_briop_rel
 * @property StrukturnoePodrazdelenie $pervoe_strukturnoe_podrazdelenie_briop_rel
 */
class FizLico extends \app\records\FizLico
{
    /**
     * @return ActiveQuery
     */
    public function getPervoe_strukturnoe_podrazdelenie_briop()
    {
        return ArrayHelper::getValue(
            $this, 
            'pervaya_rabota_fiz_lica_v_briop_rel.pervaya_dolzhnost_fiz_lica_na_rabote_rel.strukturnoe_podrazdelenie_rel'
        );
    }
    
    /**
     * @return ActiveQuery
     */
    public function getPervaya_rabota_fiz_lica_v_briop_rel()
    {
        $query = parent::getRaboty_fiz_lica_rel()
            ->where([
                'organizaciya' => Organizaciya::ID_BRIOP
            ]);

        $query->multiple = false;

        return $query;
    }

    /**
     * @param array $roli
     * @return ActiveQuery
     */
    public static function findByRoli($roli)
    {
        return static::find()
            ->joinWith('polzovateli_rel')
            ->where(['&&', 'polzovatel.roli', SqlType::encodeArraySet($roli)])
            ->groupBy('fiz_lico.id');
    }

    /**
     * @return ActiveQuery
     */
    public static function findPrepodavateli()
    {
        return static::findByRoli([
            Rol::PREPODAVATEL_KURSOV,
            Rol::RUKOVODITEL_KURSOV
        ]);
    }
}
