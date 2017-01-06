<?php
namespace app\transformers2;

use yii\helpers\FormatConverter;

use Yii;
use DateTime;

class DateTransformer extends Transformer
{
    protected function forward($value)
    {
        return Yii::$app->formatter->asDate($value);
    }

    protected function back($value)
    {
        $format = '!' . FormatConverter::convertDateIcuToPhp( Yii::$app->formatter->dateFormat );

        return DateTime::createFromFormat($format, $value)
            ->format('Y-m-d');
    }
}