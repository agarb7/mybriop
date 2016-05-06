<?php
namespace app\records;

use app\base\ActiveRecord;

/**
 * Dolzhnost record
 *
 * @property integer $id
 * @property string $nazvanie
 * @property string $tip
 * @property string $obschij
 */
class Dolzhnost extends ActiveRecord
{
    public static function tableName()
    {
        return 'dolzhnost';
    }
}