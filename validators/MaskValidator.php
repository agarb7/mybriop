<?php
namespace app\validators;

use yii\validators\RegularExpressionValidator;

class MaskValidator extends RegularExpressionValidator
{
    public $mask;
    public $sqlAttribute;

    public function init()
    {
        if ($this->message === null)
            $this->message = "«{attribute}» должен быть в формате $this->mask.";

        $qFormat = preg_quote($this->mask, '/');
        $this->pattern = '/^' . strtr($qFormat, ['9' => '\d']) . '$/';

        parent::init();
    }

    public function validateAttribute($model, $attribute)
    {
        $result = $this->validateValue($model->$attribute);
        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0], $result[1]);
        } elseif ($this->sqlAttribute !== null) {
            $sqlValue = '';

            $formatLen = strlen($this->mask);
            for ($formatPos = 0; $formatPos < $formatLen; ++$formatPos) {
                $formatCh = $this->mask[$formatPos];
                if ($formatCh === '9') {
                    $attrCh = $model->{$attribute}[$formatPos];
                    if (ctype_digit($attrCh))
                        $sqlValue .= $attrCh;
                }
            }

            $model->{$this->sqlAttribute} = $sqlValue;
        }
    }
}