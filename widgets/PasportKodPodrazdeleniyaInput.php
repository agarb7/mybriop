<?php
namespace app\widgets;

use yii\widgets\MaskedInput;
use Yii;

class PasportKodPodrazdeleniyaInput extends MaskedInput
{
    public function init()
    {
        if ($this->mask === null)
            $this->mask = Yii::$app->formatter->pasportKodPodrazdeleniyaFormat;

        parent::init();
    }
}
