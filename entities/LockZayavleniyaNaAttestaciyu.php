<?php

namespace app\entities;

/**
 * Class LockZayavleniyaNaAttestaciyu
 * @package app\entities
 *
 * @property int id
 * @property string nazvanie
 * @property string text
 */
class LockZayavleniyaNaAttestaciyu extends EntityBase
{
    public static function getNazvaniya(){
        $list = static::find()->asArray()->all();
        $result = [];
        foreach ($list as $record) {
            $result[$record['id']] = $record['nazvanie'];
        }
        return $result;
    }
}