<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 14.03.15
 * Time: 12:16
 */

namespace app\widgets;


use yii\base\Widget;

class KimTypeWidget extends Widget{

    public $params;

    public function init(){
        parent::init();
        if($this->params===null){
            $this->params = ['id'=>'kim_type'];
        }else{
            $this->params= $this->params;
        }
    }

    public function run(){
        return $this->render('kimType',['params'=>$this->params]);
    }
}