<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 02.03.15
 * Time: 17:22
 */

namespace app\widgets;


use yii\base\Model;
use yii\base\Widget;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use app\globals\ApiGlobals;

class FilesWidget extends Widget{
    public $params;
    public $model;
    public $attribute;
    /*массив параметров виджета
        id - html-идентификатор input, в который придет идентификатор файла
    */

    public function init(){
        parent::init();
        if(!$this->params){
            $this->params = ['id'=>'files'.time().rand(1,100)];
        }else{
            $this->params= $this->params;
        }
        $sql = 'select * from fajl where vladelec = :user_id';
        $polzovatel_id = ApiGlobals::getPolzovatelId();
        if (isset($this->attribute)) {
            $pureAttrbiute = BaseHtml::getAttributeName($this->attribute);
            $this->params['pureAttribute'] = $pureAttrbiute;
        }
        if (!$polzovatel_id)
            throw new \Exception;

        if ($res = \Yii::$app->db->createCommand($sql)->bindValue(':user_id', $polzovatel_id)->queryAll()) {
            $files = array();
            foreach ($res as $k=>$v) {
                $files[$v['id']] = $v;
            }
            $this->params['files'] = $files;
        }
        else $this->params['files'] = array();
        if (isset($pureAttrbiute) && isset($this->model[$pureAttrbiute]) && $this->model[$pureAttrbiute] && $this->params['files'][$this->model[$pureAttrbiute]]['vneshnee_imya_fajla'])
            $this->params['btn-text'] = $this->params['files'][$this->model[$pureAttrbiute]]['vneshnee_imya_fajla'];
        if (!isset($this->params['btn-text'])) $this->params['btn-text'] = 'Выбрать файл';
        $this->params['name'] = isset($this->model) ?
            Html::getInputName($this->model,$this->attribute) :
            $this->params['id'];
        //if (!isset($this->params['selected'])) $this->params['selected'] = null;
        //else $this->params['btn-text'] = $this->params['files'][$this->params['selected']]
        //var_dump($this->params['files']);
    }

    public function run(){
            return $this->render('files',[
                'params'=>$this->params,
                'model'=>$this->model,
                'attribute'=>$this->attribute
            ]);
    }
}