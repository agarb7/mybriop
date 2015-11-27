<?php
namespace app\widgets;

use yii\helpers\ArrayHelper;
use DateTime;

class DatePicker extends \kartik\widgets\DatePicker
{
    const DATE_PICKER_FORMAT = 'dd.mm.yyyy';
    const DATETIME_FORMAT = 'd.m.Y';

    public function __construct($config = [])
    {
        parent::__construct(ArrayHelper::merge(
            $config,
            [
                'pluginOptions' => ['format' => static::DATE_PICKER_FORMAT],
                'removeButton' => false
            ]
        ));
    }

    /**
     * @return DateTime
     */
    public function getValueAsDatetime()
    {
        return static::toDatetime($this->value);
    }

    /**
     * @param DateTime $date
     */
    public function setValueAsDatetime($date)
    {
        $this->value = static::fromDatetime($date);
    }

    /**
     * @param string|null $value
     * @return DateTime
     */
    public static function toDatetime($value)
    {
        if ($value===null)
            return null;

        return DateTime::createFromFormat(static::DATETIME_FORMAT, $value);
    }

    /**
     * @param DateTime $date
     * @return string
     */
    public static function fromDatetime($date)
    {
        if (!$date)
            return null;

        return $date->format(static::DATETIME_FORMAT);
    }
}
