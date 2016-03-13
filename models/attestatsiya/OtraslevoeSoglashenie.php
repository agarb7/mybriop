<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 07.03.16
 * Time: 21:08
 */

namespace app\models\attestatsiya;


use app\entities\OtraslevoeSoglashenieZayavleniya;
use yii\base\Model;

class OtraslevoeSoglashenie extends Model
{
    public $id;
    public $fajl;
    public $otraslevoeSoglashenie;
    public $zayavlenieNaAttestaciyu;
    public $udalit;

    public function __construct()
    {
        $this->init();
        $this->udalit = 0;
    }

    public function attributeLabels()
    {
        return [
            'fajl' => 'Подтв-ий документ',
            'otraslevoeSoglashenie' => 'Тип'
        ];
    }

    public function rules()
    {
        return [
          [['otraslevoeSoglashenie'], 'required'],
          [['fajl','id','zayavlenieNaAttestaciyu','udalit'],'safe']
        ];
    }

    public function save()
    {
        $entity = new \app\entities\OtraslevoeSoglashenieZayavleniya();
        if ($this->id){
            $entity = \app\entities\OtraslevoeSoglashenieZayavleniya::findOne($this->id);
        }
        $entity->otraslevoeSoglashenie = $this->otraslevoeSoglashenie;
        $entity->fajl = $this->fajl;
        $entity->zayavlenieNaAttestaciyu = $this->zayavlenieNaAttestaciyu;
        if ($this->udalit && $this->id){
            if ($entity->delete()) return true;
            else return false;
        }
        if (!$this->validate()) return false;
        if ($entity->save()) return $entity;
        else return false;
    }

    public static function getByZayvlenie($zayavlenieId){
        $os = OtraslevoeSoglashenieZayavleniya::find()
            ->where(['zayavlenie_na_attestaciyu' => $zayavlenieId])
            ->all();
        $result = [];
        foreach ($os as $item) {
            /**
             * @var OtraslevoeSoglashenieZayavleniya $item
             */
            $entity = new OtraslevoeSoglashenie();
            $entity->id = $item->id;
            $entity->otraslevoeSoglashenie = $item->otraslevoeSoglashenie;
            $entity->zayavlenieNaAttestaciyu = $item->zayavlenieNaAttestaciyu;
            $entity->fajl = $item->fajl;
            $result[] = $entity;
        }
        //if (empty($result)) $result[] = new OtraslevoeSoglashenie();
        return $result;
    }
}