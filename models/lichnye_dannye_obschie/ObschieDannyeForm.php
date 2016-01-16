<?php
namespace app\models\lichnye_dannye_obschie;

use app\entities\FizLico;
use app\validators\DateValidator;
use app\validators\ImyaChelovekaValidator;
use app\validators\InnValidator;
use app\validators\NazvanieValidator;
use app\validators\PasportKodPodrazdeleniyaValidator;
use app\validators\PasportNomerValidator;
use app\validators\SnilsValidator;
use app\validators\TelefonValidator;
use yii\validators\EmailValidator;
use Yii;

class ObschieDannyeForm extends FizLico
{
    public function attributeLabels()
    {
        return [
            'familiya' => 'Фамилия',
            'imya' => 'Имя',
            'otchestvo' => 'Отчество',

            'data_rozhdeniya' => 'Дата рождения',

            'pasport_no' => 'Номер',
            'pasport_kem_vydan_kod' => 'Кем выдан (код подразделения)',
            'pasport_kem_vydan' => 'Кем выдан',
            'pasport_kogda_vydan' => 'Когда выдан',

            'inn' => 'ИНН',
            'snils' => 'СНИЛС',

            'telefon' => 'Телефон',
            'email' => 'E-mail',

            'propiska' => 'Прописка'
        ];
    }

    public function rules()
    {
        return [
            ['familiya', ImyaChelovekaValidator::className()],
            ['familiya', 'required'],

            ['imya', ImyaChelovekaValidator::className()],
            ['imya', 'required'],

            ['otchestvo', ImyaChelovekaValidator::className()],
            ['otchestvo', 'default'],

            ['data_rozhdeniya', DateValidator::className(), 'sqlAttribute' => 'data_rozhdeniya'],
            ['data_rozhdeniya', 'required'],

            ['telefon', TelefonValidator::className(), 'sqlAttribute' => 'telefon'],
            ['telefon', 'required'],

            ['email', EmailValidator::className()],
            ['email', 'required'],

            ['pasport_no', PasportNomerValidator::className(), 'sqlAttribute' => 'pasport_no'],
            ['pasport_no', 'required'],

            ['pasport_kem_vydan_kod', PasportKodPodrazdeleniyaValidator::className(), 'sqlAttribute' => 'pasport_kem_vydan_kod'],
            ['pasport_kem_vydan_kod', 'required'],

            ['pasport_kem_vydan', NazvanieValidator::className()],
            ['pasport_kem_vydan', 'required'],

            ['pasport_kogda_vydan', DateValidator::className(), 'sqlAttribute' => 'pasport_kogda_vydan'],
            ['pasport_kogda_vydan', 'required'],

            ['inn', InnValidator::className(), 'sqlAttribute' => 'inn'],
            ['inn', 'required'],

            ['snils', SnilsValidator::className(), 'sqlAttribute' => 'snils'],
            ['snils', 'required'],

            ['propiska', 'required'],
            ['propiska', 'string']
        ];
    }

    public static function tableName()
    {
        return FizLico::tableName();
    }
}