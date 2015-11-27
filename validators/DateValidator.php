<?php
namespace app\validators;

class DateValidator extends \yii\validators\DateValidator
{
    public $sqlAttribute;

    public function init()
    {
        if ($this->timestampAttributeFormat === null)
            $this->timestampAttributeFormat = 'yyyy-MM-dd';

        $this->timestampAttribute = $this->sqlAttribute;

        parent::init();
    }
}