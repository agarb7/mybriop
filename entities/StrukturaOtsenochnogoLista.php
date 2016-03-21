<?php
/**
 * Created by PhpStorm.
 * User: tsyrya
 * Date: 22.02.16
 * Time: 20:30
 */

namespace app\entities;
use app\globals\ApiGlobals;

/**
 * Class StrukturaOtsenochnogoLista
 * @package app\entities
 *
 * @property int id
 * @property int otsenochnyjList
 * @property string nazvanie
 * @property int bally
 * @property int nomer
 * @property int roditel
 */
class StrukturaOtsenochnogoLista extends EntityBase
{
    public function getOtsenochnyjListRel()
    {
        return $this->hasOne(OtsenochnyjList::className(),['id' => 'otsenochnyj_list']);
    }

    public function getPodstrukturaRel(){
        return $this->hasMany(StrukturaOtsenochnogoLista::className(), ['roditel' => 'id'])->from(StrukturaOtsenochnogoLista::tableName() . ' podstruktura');
    }

    public static function recalculateSummuBallov($id){
        $bally = StrukturaOtsenochnogoLista::find()->where(['roditel'=>$id])->sum('bally');
        if (!$bally) $bally = 1;
        $roditel = StrukturaOtsenochnogoLista::findOne($id);
        $roditel->bally = $bally;
        if ($roditel->save()) return true;
        else return false;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            $this->nazvanie = ApiGlobals::to_trimmed_text($this->nazvanie);
            return true;
        }
        else{
            return false;
        }
    }
}