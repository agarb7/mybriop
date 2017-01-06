<?php
namespace app\models\lichnye_dannye_rabota;

use app\behaviors\DirectoryBehavior;
use app\entities\RabotaFizLica;
use app\enums\OrgTipRaboty;
use app\validators\ComboValidator;
use app\validators\EnumValidator;
use app\validators\TelefonValidator;

class RabotaForm extends RabotaFizLica
{
    public function behaviors()
    {
        return [
            'directories' => [
                'class' => DirectoryBehavior::className(),
                'directoryAttributes' => ['organizaciyaRel' => 'organizaciyaDir']
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'organizaciyaDir' => 'Организация',
            'org_tip' => 'Совместительство',
            'telefon' => 'Телефон'
        ];
    }

    public function rules()
    {
        return [
            ['organizaciyaDir', ComboValidator::className(), 'directoryAttribute' => 'organizaciyaDir', 'required' => true],
            ['org_tip', EnumValidator::className(), 'enumClass' => OrgTipRaboty::className()],
            ['telefon', TelefonValidator::className(), 'sqlAttribute' => 'telefon']
        ];
    }

    public function canDelete()
    {
        return !$this
            ->getDolzhnostiFizLicaNaRaboteRel()
            ->exists();
    }

    public static function tableName()
    {
        return RabotaFizLica::tableName();
    }
}