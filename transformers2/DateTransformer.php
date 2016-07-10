<?php
namespace app\transformers2;

use DateTime;

use Yii;
use yii\helpers\FormatConverter;

class DateTransformer extends Transformer
{
    public function transform($value)
    {
        if ($value === null)
            return null;
        
        return Yii::$app->formatter->asDate($value);
    }

    public function backTransform($value)
    {
        $format = '!' . FormatConverter::convertDateIcuToPhp( Yii::$app->formatter->dateFormat );

        return DateTime::createFromFormat($format, $value)
            ->format('Y-m-d');
    }
}