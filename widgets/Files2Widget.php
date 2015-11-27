<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 10.10.15
 * Time: 18:33
 */

namespace app\widgets;


use app\helpers\Html;
use yii\base\Widget;
use yii\helpers\BaseHtml;

class Files2Widget extends Widget
{
    public $name;
    public $id;
    public $model;
    public $attribute;
    public $file_id;
    public $pure_attribute;
    public $select_callback;
    public $options;
    public $caption;
    /*массив параметров виджета
        id - html-идентификатор input, в который придет идентификатор файла
    */

    public function init()
    {
        parent::init();
        $this->id = $this->id ? $this->id : 'files'.time().rand(1,100);
        //var_dump($this->id);
        $this->name = isset($this->model) ?
            Html::getInputName($this->model,$this->attribute) :
            $this->id;
        if (isset($this->attribute))
            $this->pure_attribute = BaseHtml::getAttributeName($this->attribute);
        if (!$this->file_id){
            if (isset($this->attribute)) $this->file_id = $this->model[$this->pure_attribute];
            else $this->file_id = -1;
        }
        if (!$this->options) $this->options = [];
        if (!$this->select_callback) $this->select_callback = false;
        if (!$this->caption) $this->caption = false;
    }

    public function run(){
        return $this->render('files2',[
            'name' => $this->name,
            'id' => $this->id,
            'model' => $this->model,
            'attribute' => $this->attribute,
            'file_id' => $this->file_id,
            'pure_attribute' => $this->pure_attribute,
            'select_callback' =>$this->select_callback,
            'options' => $this->options,
            'caption' => $this->caption
        ]);
    }

}