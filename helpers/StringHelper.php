<?php
namespace app\helpers;

use yii\helpers\Json;

class StringHelper extends \yii\helpers\StringHelper
{
    public static function onlyDigits($str)
    {
        return mb_ereg_replace('\D', '', $str);
    }

    public static function squeezeLine($str)
    {
        $trimmed = self::trim($str);
        return mb_ereg_replace('\s+', ' ', $trimmed);
    }

    public static function squeezeText($text)
    {
        $trimmed = self::trim($text);
        $nl_squeezed = mb_ereg_replace('\s*\n\s*', "\n", $trimmed);
        return mb_ereg_replace('[^\n\S]+', ' ', $nl_squeezed);
    }

    public static function trim($str)
    {
        return mb_ereg_replace('^\s*(.*?)\s*$', '\1', $str);
    }

    //todo test this
    public static function strip($str, $left, $right)
    {
        $qleft = preg_quote($left);
        $qright = preg_quote($right);
        $pattern = '^\s*' . $qleft . '\s*(.*?)\s*' . $qright .'\s*$';

        return mb_ereg_replace($pattern, '\1', $str);
    }

    public static function charCode($char)
    {
        $saved_lang = mb_language();
        $saved_int_enc = mb_internal_encoding();

        mb_language('Neutral');
        mb_internal_encoding('UTF-8');

        $ar = unpack('N', mb_convert_encoding($char, 'UCS-4BE', 'UTF-8'));
        $res = is_array($ar) ? $ar[1]: ord($char);

        mb_language($saved_lang);
        mb_internal_encoding($saved_int_enc);

        return $res;
    }

    public static function nbsp()
    {
        return Json::decode('"\u00A0"');
    }
}
