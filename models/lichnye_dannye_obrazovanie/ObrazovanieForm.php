<?php
namespace app\models\lichnye_dannye_obrazovanie;

use app\behaviors\DirectoryBehavior;
use app\entities\ObrazovanieFizLica;
use app\enums\TipDokumentaObObrazovanii;
use app\enums\TipKursa;
use app\validators\ChasyObucheniyaValidator;
use app\validators\ComboValidator;
use app\validators\DateValidator;
use app\validators\EnumValidator;
use app\validators\NazvanieValidator;
use app\validators\NomerDokumentaValidator;
use Yii;

class ObrazovanieForm extends ObrazovanieFizLica
{
    public function behaviors()
    {
        return [
            'directories' => [
                'class' => DirectoryBehavior::className(),
                'directoryAttributes' => [
                    'organizaciyaRel' => 'organizaciyaDir',
                    'kvalifikaciyaRel' => 'kvalifikaciyaDir'
                ]
            ]
        ];
    }

    public function rules()
    {
        return [
            ['dokument_ob_obrazovanii_tip', EnumValidator::className(), 'enumClass' => TipDokumentaObObrazovanii::className()],
            ['dokument_ob_obrazovanii_tip', 'required'],

            ['dokument_ob_obrazovanii_seriya', NomerDokumentaValidator::className()],
            ['dokument_ob_obrazovanii_seriya', 'default'],

            ['dokument_ob_obrazovanii_nomer', NomerDokumentaValidator::className()],
            ['dokument_ob_obrazovanii_nomer', 'required'],

            ['dokument_ob_obrazovanii_data', DateValidator::className(), 'sqlAttribute' => 'dokument_ob_obrazovanii_data'],
            ['dokument_ob_obrazovanii_data', 'default'],

            ['kvalifikaciyaDir', ComboValidator::className(), 'directoryAttribute' => 'kvalifikaciyaDir', 'required' => true],

            ['organizaciyaDir', ComboValidator::className(), 'directoryAttribute' => 'organizaciyaDir', 'required' => true],

            ['kurs_tip', EnumValidator::className(), 'enumClass' => TipKursa::className()],
            ['kurs_tip', 'default'],

            ['kurs_nazvanie', NazvanieValidator::className()],
            ['kurs_nazvanie', 'default'],

            ['kurs_chasy', ChasyObucheniyaValidator::className()],
            ['kurs_chasy', 'default']
        ];
    }

    public function attributeLabels()
    {
        return [
            'dokument_ob_obrazovanii_tip' => 'Тип',
            'dokument_ob_obrazovanii_seriya' => 'Серия',
            'dokument_ob_obrazovanii_nomer' => 'Номер',
            'dokument_ob_obrazovanii_data' => 'Дата выдачи',

            'kvalifikaciyaDir' => 'Квалификация',

            'organizaciyaDir' => 'Образовательное учреждение',

            'kurs_tip' => 'Тип курса',
            'kurs_nazvanie' => 'Название курса',
            'kurs_chasy' => 'Объём часов'
        ];
    }

    public static function tableName()
    {
        return ObrazovanieFizLica::tableName();
    }
}
