<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 07.08.15
 * Time: 12:19
 */

namespace app\validators;


use yii\validators\Validator;

class AttestaciyaOrganizaciyaValidator extends Validator
{
    public function validateAttribute($model,$attribute){
       //if (!$object->$attribute)
       if (!$model->organizaciyaId && !$model->organizaciyaNazvanie) {
           $message = $this->message ? $this->message : 'Ошибка';
           $this->addError($model, $attribute, $message);
       }
    }

//    public function clientValidateAttribute($model, $attribute, $view)
//    {
//        $message = $this->message ? $this->message : 'Ошибка';
//        $message = json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
//        $organizaciyaId = $model->organizaciyaId;
//        $organizaciyaNazvanie = $model->organizaciyaNazvanie;
//        return <<<JS
//            var organizaciyaId = '$organizaciyaId';
//            var organizaciyaNazvanie = '$organizaciyaNazvanie';
//            if (!organizaciyaId && !organizaciyaNazvanie) {
//                            messages.push($message);
//                        }
//JS;
//    }

}