<?php
namespace app\helpers;

use Yii;

class Hashids
{
    /**
     * Decode $hashids and return first item. If can't decode then return false.
     * @param $hashids hashids to be decoded
     * @return bool|integer id, or false if can't decode
     */
    public static function decodeOne($hashids)
    {
        $ids = Yii::$app->hashids->decode($hashids);
        if (!$ids)
            return false;

        return $ids[0];
    }
    
    public static function codeOne($id)
    {
        return Yii::$app->hashids->encode($id);
    }

}