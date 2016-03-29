<?php
namespace app\widgets;

use app\components\Formatter;
use Yii;

class DatePicker extends \kartik\widgets\DatePicker
{
    public function init()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        if (!isset($this->pluginOptions['format']))
            $this->pluginOptions['format'] = 'dd.mm.yyyy';

        if (!isset($this->pluginOptions['clearBtn']))
            $this->pluginOptions['clearBtn'] = true;

        if (!isset($this->pluginOptions['autoclose']))
            $this->pluginOptions['autoclose'] = true;

        $this->removeButton = false;

        $formatter->nullDisplay = '';
        $this->model->{$this->attribute} = $formatter->asDate($this->model->{$this->attribute});

        parent::init();
    }
}
