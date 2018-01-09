<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 10.08.15
 * Time: 11:33
 */

namespace app\entities;

/**
 * Class VremyaProvedeniyaAttestacii
 * @package app\entities
 * @property int $id
 * @property date priem_zayavleniya_nachalo
 * @property date priem_zayavleniya_konec
 * @property date nachalo
 * @property date konec
 */
class VremyaProvedeniyaAttestacii extends EntityBase
{

    public function getZayavlenieNaAttestaciyuRel(){
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['vremya_provedeniya'=>'id'])->inverseOf('vremyaProvedeniyaAttestaciiRel');
    }

    public static function getItemsToSelect($onlynew = false, $currentVremya = false){
        if ($onlynew) {
            if ($currentVremya) {
                $items = static::find()->where(['=', '(extract (year from priem_zayavleniya_konec))', date('Y')])->andWhere(['>=', '(extract(month from priem_zayavleniya_nachalo))', date('m') - 1])->orWhere(['id' => $currentVremya])->orderBy('nachalo')->all();
            }
            else{
                $items = static::find()->where(['=', '(extract (year from priem_zayavleniya_konec))', date('Y')])->andWhere(['>=', '(extract(month from priem_zayavleniya_nachalo))', date('m') - 1])->orWhere(['>', '(extract (year from priem_zayavleniya_nachalo))', date('Y')])->orderBy('nachalo')->all();
            }
        }
        else {
            $items = static::find()->orderBy('nachalo')->where(['>','nachalo','20160830'])->all();
        }
        $result = [];
        foreach ($items as $k=>$v) {
            $result[$v->id] = 'Прием заявлений с '.\Yii::$app->formatter->asDate($v->priem_zayavleniya_nachalo,'php:d.m.Y').' по '.\Yii::$app->formatter->asDate($v->priem_zayavleniya_konec,'php:d.m.Y').', '.
                              'прохождения аттестации с '.\Yii::$app->formatter->asDate($v->nachalo,'php:d.m.Y').' по '.\Yii::$app->formatter->asDate($v->konec,'php:d.m.Y');
        }
        return $result;
    }

    public static function getItemsToSelectFromSeptember(){
        $items = static::find()->where(['>=', '(extract (year from nachalo))', 2016])->andWhere(['>=', '(extract(month from nachalo))', 9])->orderBy('nachalo')->all();
        $result = [];
        foreach ($items as $k=>$v) {
            $result[$v->id] = 'Прием заявлений с '.\Yii::$app->formatter->asDate($v->priem_zayavleniya_nachalo,'php:d.m.Y').' по '.\Yii::$app->formatter->asDate($v->priem_zayavleniya_konec,'php:d.m.Y').', '.
                'прохождения аттестации с '.\Yii::$app->formatter->asDate($v->nachalo,'php:d.m.Y').' по '.\Yii::$app->formatter->asDate($v->konec,'php:d.m.Y');
        }
        return $result;
    }

}