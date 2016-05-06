<?php
namespace app\upravlenie_kursami\models;

use app\base\ActiveQuery;
use app\enums2\Rol;
use app\helpers\SqlType;

class FizLico extends \app\records\FizLico
{
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