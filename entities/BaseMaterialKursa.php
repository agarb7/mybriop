<?php
namespace app\entities;

use app\helpers\Val;

/**
 * Class BaseMaterialKursa. It is base for Kim and Umk record
 * @property integer $id
 * @property string $opisanie
 * @property integer $fajl
 * @property string $uri
 */
class BaseMaterialKursa extends EntityBase
{
    const TYPE_FAJL = 'fajl';
    const TYPE_URI = 'uri';

    public static function tableName()
    {
        return null;
    }

    public function getType()
    {
        if ($this->uri)
            return self::TYPE_URI;

        if ($this->fajl)
            return self::TYPE_FAJL;

        return null;
    }

    public function getFajlRel()
    {
        return $this->hasOne(Fajl::className(), ['id' => 'fajl']);
    }
}