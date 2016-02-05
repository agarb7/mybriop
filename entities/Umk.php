<?php
namespace app\entities;

/**
 * Umk record
 */
class Umk extends BaseMaterialKursa
{
    public static function tableName()
    {
        return 'umk';
    }

    public function isUsed(){
        $umk_kurs_count = UmkKursa::find()->where(['umk'=>$this->id])->count();
        $umk_podrazdela_count = UmkPodrazdelaKursa::find()->where(['umk'=>$this->id])->count();
        $umk_temy_count = UmkTemy::find()->where(['umk'=>$this->id])->count();
        return $umk_kurs_count + $umk_podrazdela_count + $umk_temy_count > 0 ? true
                                                                             : false;
    }
}