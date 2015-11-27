<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 19.03.15
 * Time: 17:07
 */

namespace app\widgets;

use yii\base\Widget;


class UmkTypeWidget extends Widget {
    public $params;

    public function init(){
        parent::init();
        if($this->params===null){
            $this->params = ['id'=>'umk_type'];
        }else{
            $this->params= $this->params;
        }
    }

    public function run(){
        return $this->render('umkType',['params'=>$this->params]);
    }
}