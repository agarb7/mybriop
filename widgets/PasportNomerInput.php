<?php
namespace app\widgets;

use app\base\Formatter;
use yii\widgets\MaskedInput;
use Yii;

class PasportNomerInput extends MaskedInput
{
    public function init()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        if ($this->mask === null)
            $this->mask = Yii::$app->formatter->pasportNomerFormat;

        parent::init();

        $formatter->nullDisplay = '';
        $this->model->{$this->attribute} = $formatter->asPasportNomer($this->model->{$this->attribute});
    }
}
