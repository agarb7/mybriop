<?php
namespace app\widgets;

use app\components\Formatter;
use yii\widgets\MaskedInput;
use Yii;

class TelefonInput extends MaskedInput
{
    public function init()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        if ($this->mask === null)
            $this->mask = $formatter->telefonFormat;

        parent::init();

        $formatter->nullDisplay = '';
        $this->model->{$this->attribute} = $formatter->asTelefon($this->model->{$this->attribute});
    }
}