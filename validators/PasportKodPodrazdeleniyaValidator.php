<?php
namespace app\validators;

use Yii;

class PasportKodPodrazdeleniyaValidator extends MaskValidator
{
    public $pasportKodPodrazdeleniyaFormat;

    public function init()
    {
        if ($this->pasportKodPodrazdeleniyaFormat === null)
            $this->pasportKodPodrazdeleniyaFormat = Yii::$app->formatter->pasportKodPodrazdeleniyaFormat;

        $this->mask = $this->pasportKodPodrazdeleniyaFormat;
        parent::init();
    }
}
