<?php
namespace app\validators;

use Yii;

class TelefonValidator extends MaskValidator
{
    public $telefonFormat;

    public function init()
    {
        if ($this->telefonFormat === null)
            $this->telefonFormat = Yii::$app->formatter->telefonFormat;

        $this->mask = $this->telefonFormat;
        parent::init();
    }
}
