<?php
namespace app\widgets;

use yii\widgets\MaskedInput;
use Yii;

class PasportNomerInput extends MaskedInput
{
    public function init()
    {
        if ($this->mask === null)
            $this->mask = Yii::$app->formatter->pasportNomerFormat;

        parent::init();
    }
}
