<?php

/**
 * Class RabotnikKomissii
 */
namespace app\models\rukovoditel_attestacionnoj_komissii;

class RabotnikKomissii extends \yii\base\Model
{
    public function __construct(Array $properties=array()){
        parent::init();
        foreach($properties as $key => $value){
            $this->{$key} = $value;
        }
    }

    public $rabotnikId;
    public $familiya;
    public $imya;
    public $otchestvo;
    public $fizLico;
    public $checked = false;

    public function getFio(){
        return $this->familiya.' '.$this->imya.' '.$this->otchestvo;
    }

    public function rules()
    {
        return [
            [['rabotnikId','familiya','imya','otchestvo'],'default']
        ];
    }


}