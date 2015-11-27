<?php
namespace app\validators;

use Yii;

class SnilsValidator extends MaskValidator
{
    public $snilsFormat;

    public function init()
    {
        if ($this->snilsFormat === null)
            $this->snilsFormat = Yii::$app->formatter->snilsFormat;

        $this->mask = $this->snilsFormat;
        parent::init();
    }
}