<?php
namespace app\upravlenie_kursami\models;

use app\base\ActiveQuery;
use app\enums2\Rol;
use app\helpers\SqlType;
use app\records\Organizaciya;

class FizLico extends \app\records\FizLico
{
    /**
     * @return ActiveQuery
     */
    public function getBriop_raboty_fiz_lica_rel()
    {
        return parent::getRaboty_fiz_lica_rel()
            ->where(['organizaciya' => Organizaciya::ID_BRIOP]);
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
