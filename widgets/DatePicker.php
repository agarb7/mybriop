<?php
namespace app\widgets;

use app\base\Formatter;
use Yii;

class DatePicker extends \kartik\widgets\DatePicker
{
    public function init()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        $this->pluginOptions['format'] = 'dd.mm.yyyy';
        $this->removeButton = false;

        $formatter->nullDisplay = '';
        $this->model->{$this->attribute} = $formatter->asDate($this->model->{$this->attribute});

        parent::init();
    }
}
