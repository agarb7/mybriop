<?php
namespace app\validators;

use Yii;

class InnValidator extends MaskValidator
{
    public $innFormat;

    public function init()
    {
        if ($this->innFormat === null)
            $this->innFormat = Yii::$app->formatter->innFormat;

        $this->mask = $this->innFormat;
        parent::init();
    }
}