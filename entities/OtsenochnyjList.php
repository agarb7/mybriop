<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 22.02.16
 * Time: 20:00
 */

namespace app\entities;
use app\globals\ApiGlobals;

/**
 * Class OtsenochnyjList
 * @package app\entities
 *
 * @property int id
 * @property string nazvanie
 * @property int minBallPervayaKategoriya
 * @property int minBallVisshayaKategoriya
 */
class OtsenochnyjList extends EntityBase
{
    function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            $this->nazvanie = ApiGlobals::to_trimmed_text($this->nazvanie);
            return true;
        }
        else{
            return false;
        }
    }

    public function  rules()
    {
        return [
          [['nazvanie','minBallPervayaKategoriya','minBallVisshayaKategoriya'],'safe']
        ];
    }

    public function getIspytanieOtsenochnogoListaRel(){
        return $this->hasMany(IspytanieOtsenochnogoLista::className(),['otsenochnyj_list'=>'id']);
    }

    public function getAttKomissiiOtsenochnogoListaRel(){
        return $this->hasMany(AttKomissiiOtsenochnogoLista::className(),['otsenochnyj_list_id' => 'id']);
    }

}