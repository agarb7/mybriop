<?php
namespace app\widgets;

use app\base\Formatter;
use yii\widgets\MaskedInput;
use Yii;

class PasportKodPodrazdeleniyaInput extends MaskedInput
{
    public function init()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        if ($this->mask === null)
            $this->mask = Yii::$app->formatter->pasportKodPodrazdeleniyaFormat;

        parent::init();

        $formatter->nullDisplay = '';
        $this->model->{$this->attribute} = $formatter->asPasportKodPodrazdeleniya($this->model->{$this->attribute});
    }
}
