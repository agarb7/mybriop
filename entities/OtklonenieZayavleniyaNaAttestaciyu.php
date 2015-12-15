<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 06.12.15
 * Time: 22:10
 */

namespace app\entities;

/**
 * Class OtklonenieZayavleniyaNaAttestaciyu
 * @package app\entities
 *
 * @property int id
 * @property string nazvanie
 * @property string text
 */
class OtklonenieZayavleniyaNaAttestaciyu extends EntityBase
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