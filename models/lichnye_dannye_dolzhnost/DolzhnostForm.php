<?php
namespace app\models\lichnye_dannye_dolzhnost;

use app\behaviors\DirectoryBehavior;
use app\entities\DolzhnostFizLicaNaRabote;
use app\enums\EtapObrazovaniya;
use app\enums\OrgTipDolzhnosti;
use app\validators\ComboValidator;
use app\validators\EnumValidator;
use app\validators\StazhValidator;

class DolzhnostForm extends DolzhnostFizLicaNaRabote
{
    public function behaviors()
    {
        return [
            'directories' => [
                'class' => DirectoryBehavior::className(),
                'directoryAttributes' => [
                    'dolzhnostRel' => 'dolzhnostDir',
                ]
            ]
        ];
    }

    public function rules()
    {
        return [
            ['dolzhnostDir', ComboValidator::className(), 'directoryAttribute' => 'dolzhnostDir', 'required' => true],
            ['dolzhnostDir', 'required'],

            ['org_tip', EnumValidator::className(), 'enumClass' => OrgTipDolzhnosti::className()],
            ['org_tip', 'required'],

            ['etap_obrazovaniya', EnumValidator::className(), 'enumClass' => EtapObrazovaniya::className()],
            ['etap_obrazovaniya', 'default'],

            ['stazh', StazhValidator::className()],
            ['stazh', 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'dolzhnostDir' => 'Должность',
            'org_tip' => 'Совмещение',
            'etap_obrazovaniya' => 'Этап образования',
            'stazh' => 'Стаж',
        ];
    }

    public static function tableName()
    {
        return DolzhnostFizLicaNaRabote::tableName();
    }

}