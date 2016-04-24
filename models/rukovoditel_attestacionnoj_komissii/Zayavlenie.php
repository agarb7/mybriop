<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 11.01.16
 * Time: 20:32
 */

namespace app\models\rukovoditel_attestacionnoj_komissii;


use app\entities\RaspredelenieZayavlenijNaAttestaciyu;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class Zayavlenie
 * @package app\models\rukovoditel_attestacionnoj_komissii
 *
 * @property int $id
 * @property string $familiya
 * @property string $imya
 * @property string $otchestvo
 * @property array $raspredelenie - array of rabotnik_atestacionnoj_komissii_id
 *
 */
class Zayavlenie extends Model
{
    public $id = false;
    public $familiya = false;
    public $imya = false;
    public $otchestvo = false;
    public $raspredelenie=[];
    public $raspredelenieCopy=[];
    public $otsenki = [];
    public $statuses = []; //Статус оценок одного оценивающего

    public function __construct(Array $properties=array()){
        parent::init();
        foreach($properties as $key => $value){
            if (isset($this->{$key}))
            $this->{$key} = $value;
        }
    }

    public function rules(){
        return [
          [['id','familiya','imya','otchestvo','raspredelenie','raspredelenieCopy'],'default']
        ];
    }

    public function saveRaspredelenie(){
        $deleting = [];
        foreach ($this->raspredelenie as $item) {
            $key = array_search($item,$this->raspredelenieCopy);
            if ($key !== false) unset($this->raspredelenieCopy[$key]);
            else{
                $deleting[] = $item;
            }
        }

        if ($deleting and !RaspredelenieZayavlenijNaAttestaciyu::deleteAll
        (
            ['and','zayavlenie_na_attestaciyu=:zayavlenie',['in','rabotnik_attestacionnoj_komissii',$deleting]],
            [':zayavlenie' => $this->id]
        )) {
            throw new Exception('Error occured while raspredelenie_zayavlenij_na_attestaciyu was deleting');
            return false;
        }

        foreach ($this->raspredelenieCopy as $item) {
            if (!\Yii::$app->db->createCommand()->insert('raspredelenie_zayavlenij_na_attestaciyu',[
                'zayavlenie_na_attestaciyu'=>$this->id,'rabotnik_attestacionnoj_komissii'=>$item
            ])->execute()) {
                throw new Exception('Error occured while items were inserting to raspredelenie_zayavlenij_na_attestaciyu');
                return false;
            }
        }

        return true;
    }
}