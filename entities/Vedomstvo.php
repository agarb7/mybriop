<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.07.15
 * Time: 12:13
 */

namespace app\entities;

/**
 * Class Vedomstvo
 * @package app\models\entities
 *
 * @property string $id bigserial NOT NULL,
 * @property string $nazvanie nazvanie NOT NULL,
 * @property string $sokraschyonnoeNazvanie nazvanie, -- принятое сокращённое название ведомства
 * @property string $roditel bigint, -- ссылка на вышестоящее ведомство
 */
class Vedomstvo extends EntityBase
{
    public function getOrganizaciiRel()
    {
        return $this->hasMany(Organizaciya::className(), ['vedomstvo' => 'id'])->inverseOf('vedomstvoRel');
    }

    public function getRoditelRel()
    {
        return $this->hasOne(Vedomstvo::className(), ['id' => 'roditel'])->inverseOf('detiRel');
    }

    public function getDetiRel()
    {
        return $this->hasMany(Vedomstvo::className(), ['roditel' => 'id'])->inverseOf('roditelRel');
    }
}
