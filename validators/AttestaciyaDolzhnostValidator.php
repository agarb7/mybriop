<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 07.08.15
 * Time: 21:49
 */

namespace app\validators;


use app\entities\Dolzhnost;
use yii\validators\Validator;

class AttestaciyaDolzhnostValidator extends Validator
{
    public function validateAttribute($model,$attribute){
        if ($model->organizaciyaId) {
            $dolzhnost = Dolzhnost::getDolzhnostFizLica($model->fizLicoId, $model->$attribute,$model->organizaciyaId)->one();
            if ($dolzhnost)
                $this->addError($model, $attribute, 'Данная должность уже присутствует в списке');
        }
    }
}