<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 30.08.15
 * Time: 20:00
 */

namespace app\widgets;


use yii\base\Widget;

class Select3 extends Widget
{
    public $model;
    public $attribute;
    public $secondAttribute;
    public $data;
    public $placeholder;
    public $secondPlaceholder;

    function init(){
        parent::init();
        if (!isset($this->placeholder))
            $this->placeholder = '';
        if (!isset($this->seconPlaceholder))
            $this->secondPlaceholder = '';
    }

    function run(){
        return $this->render('select3',[
            'model' => $this->model,
            'attribute' => $this->attribute,
            'secondAttribute' => $this->secondAttribute,
            'data' => $this->data,
            'placeholder' => $this->placeholder,
            'secondPlaceholder' => $this->secondPlaceholder
        ]);
    }
}