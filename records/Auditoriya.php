<?php
namespace app\records;

use app\base\ActiveRecord;

/**
 * Auditoriya record
 *
 * @property integer $id
 * @property string $nazvanie
 * @property boolean $obschij
 */
class Auditoriya extends ActiveRecord
{
    public static function tableName()
    {
        return 'auditoriya';
    }
}
