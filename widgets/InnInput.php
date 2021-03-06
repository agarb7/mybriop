<?php
namespace app\widgets;

use app\components\Formatter;
use yii\widgets\MaskedInput;
use Yii;

class InnInput extends MaskedInput
{
    public function init()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        if ($this->mask === null)
            $this->mask = Yii::$app->formatter->innFormat;

        parent::init();

        $formatter->nullDisplay = '';
        $this->model->{$this->attribute} = $formatter->asInn($this->model->{$this->attribute});
    }
}