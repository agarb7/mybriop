<?php
namespace app\validators;

class DateValidator extends \yii\validators\DateValidator
{
    public $sqlAttribute;

    public function init()
    {
        if ($this->timestampAttributeFormat === null)
            $this->timestampAttributeFormat = 'yyyy-MM-dd';

        if ($this->timestampAttribute === null)
            $this->timestampAttribute = $this->sqlAttribute;

        parent::init();
    }
}