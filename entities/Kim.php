<?php
namespace app\entities;

/**
 * Kim record
 * @property string $text
 * @property integer $id
 */
class Kim extends BaseMaterialKursa
{
    const TYPE_TEXT = 'text';

    public static function tableName()
    {
        return 'kim';
    }

    public function getType()
    {
        if ($this->text)
            return self::TYPE_TEXT;

        return parent::getType();
    }

    public function isUsed(){
        $kim_kurs_count = KimKursa::find()->where(['kim'=>$this->id])->count();
        $kim_podrazdela_count = KimPodrazdelaKursa::find()->where(['kim'=>$this->id])->count();
        $kim_temy_count = KimTemy::find()->where(['kim'=>$this->id])->count();
        return $kim_kurs_count + $kim_podrazdela_count + $kim_temy_count > 0
                                                                    ? true
                                                                    : false;
    }
}