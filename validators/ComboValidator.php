<?php
namespace app\validators;

use app\base\Formatter;
use app\helpers\StringHelper;
use yii\base\InvalidParamException;
use yii\helpers\Json;
use yii\validators\RequiredValidator;
use yii\validators\Validator;

class ComboValidator extends Validator
{
    public $required = false;

    public $targetAttribute;

    public function validateAttribute($model, $attribute)
    {
        $result = $this->validateValue($model->$attribute);

        if (!empty($result)) {
            $this->addError($model, $attribute, $result[0]);
            return;
        }

        if ($this->targetAttribute !== null)
            $model->{$this->targetAttribute} = $this->_targetValue;
    }

    protected function validateValue($value)
    {
        $badStructure = 'Неправильная структура данных.';

        try {
            $decoded = Json::decode($value);
        } catch (InvalidParamException $e) {
            return [$badStructure];
        }

        if (!array_key_exists(0, $decoded) || !array_key_exists(1, $decoded))
            return [$badStructure];

        $state = $decoded[0];
        $value = $decoded[1];

        if (!in_array($state, [Formatter::COMBO_STATE_LIST, Formatter::COMBO_STATE_TEXT]))
            return [$badStructure];

        if ($this->required === true) {
            $validator = new RequiredValidator;
            $error = null;
            if (!$validator->validate($value, $error))
                return [$error];
        }

        if ($value === null || $value === '') {
            $this->_targetValue = null;
            return null;
        }

        if ($state === Formatter::COMBO_STATE_LIST) {
            $this->_targetValue = ['id' => (int) $value];
            return null;
        }

        if ($state === Formatter::COMBO_STATE_TEXT) {
            $value = StringHelper::squeezeLine($value);

            $validator = new NazvanieValidator;
            $error = null;
            if (!$validator->validate($value, $error))
                return [$error];

            $this->_targetValue = ['nazvanie' => $value];
            return null;
        }

        return [$badStructure];
    }

    private $_targetValue;
}