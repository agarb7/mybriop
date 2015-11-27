<?php
namespace app\transformers;

use Yii;
use DateTime;

class DatetimeTransformer extends Transformer
{
    /**
     * @inheritdoc
     */
    protected function transformToValue($prop_value)
    {
        if ($prop_value === null)
            return null;

        return Yii::$app->formatter->asDatetime($prop_value, 'yyyy-MM-dd HH:mm:ss');
    }

    /**
     * @inheritdoc
     */
    protected function transformFromValue($col_value)
    {
        if ($col_value === null)
            return null;

        return new DateTime($col_value);
    }
}