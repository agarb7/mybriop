<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 21.03.15
 * Time: 16:01
 */

namespace app\widgets;


use yii\base\Widget;

class MultipleSelect extends Widget{
    public $params;
    public function init(){
        parent::init();
        if($this->params===null){
            $this->params = ['id'=>'ms'];
            $this->params['data'] = [];
        }else{
            $this->params= $this->params;
        }
    }

    public function run(){
        return $this->render('multipleSelect',['params'=>$this->params]);
    }
}