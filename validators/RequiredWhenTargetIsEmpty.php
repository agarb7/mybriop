<?php
namespace app\validators;

use yii\helpers\Html;
use yii\validators\RequiredValidator;

class RequiredWhenTargetIsEmpty extends RequiredValidator
{
    public $targetModel;
    public $targetAttribute;

    public function init()
    {
        parent::init();

        //todo using strict options
        $this->when = function () {
            $oth_val = $this->targetModel->{$this->targetAttribute};
            return $this->isEmpty(is_string($oth_val) ? trim($oth_val) : $oth_val);
        };

        //todo using strict options
        $id = Html::getInputId($this->targetModel, $this->targetAttribute);
        $this->whenClient = "function(){return $('#$id').prop('disabled');}";
    }
}