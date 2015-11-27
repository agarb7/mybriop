<?php
namespace app\validators;

use app\helpers\Hashids;
use yii\validators\Validator;
use Yii;

class HashidsValidator extends Validator
{
    public $targetAttribute;
    public $onlyOne = true;

    public function validateAttribute($model, $attribute)
    {
        $hash = $model->$attribute;

        $res = $this->onlyOne
            ? Hashids::decodeOne($hash)
            : Yii::$app->hashids->decode($hash);

        if (!$res) {
            $this->addError($model, $attribute, 'This hash can not be dencoded');
            return;
        }

        if ($this->targetAttribute)
            $model->{$this->targetAttribute} = $res;
    }
}