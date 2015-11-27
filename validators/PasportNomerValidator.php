<?php
namespace app\validators;

use Yii;

class PasportNomerValidator extends MaskValidator
{
    public $pasportNomerFormat;

    public function init()
    {
        if ($this->pasportNomerFormat === null)
            $this->pasportNomerFormat = Yii::$app->formatter->pasportNomerFormat;

        $this->mask = $this->pasportNomerFormat;
        parent::init();
    }
}
