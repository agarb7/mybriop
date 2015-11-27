<?php
namespace app\widgets;

use yii\widgets\MaskedInput;
use Yii;

class TelefonInput extends MaskedInput
{
    public function init()
    {
        if ($this->mask === null)
            $this->mask = Yii::$app->formatter->telefonFormat;

        parent::init();
    }
}