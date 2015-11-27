<?php
namespace app\models\lichnye_dannye;

use app\base\Formatter;
use app\entities\FizLico;
use app\validators\DateValidator;
use app\validators\ImyaChelovekaValidator;
use app\validators\InnValidator;
use app\validators\NazvanieValidator;
use app\validators\PasportKodPodrazdeleniyaValidator;
use app\validators\PasportNomerValidator;
use app\validators\SnilsValidator;
use app\validators\TelefonValidator;
use yii\base\Model;
use Yii;
use yii\validators\EmailValidator;

//todo kopiya_trudovoj_knizhki
//todo structured propiska: fias, dom, kvartira

class ObschieDannyeForm extends Model
{
    public $familiya;
    public $imya;
    public $otchestvo;

    public $dataRozhdeniya;
    public $dataRozhdeniyaSql;

    public $pasportNo;
    public $pasportNoSql;
    public $pasportKemVydanKod;
    public $pasportKemVydanKodSql;
    public $pasportKemVydan;
    public $pasportKogdaVydan;
    public $pasportKogdaVydanSql;

    public $inn;
    public $innSql;
    public $snils;
    public $snilsSql;

    public $telefon;
    public $telefonSql;
    public $email;

    public $propiska;

    public function attributeLabels()
    {
        return [
            'familiya' => 'Фамилия',
            'imya' => 'Имя',
            'otchestvo' => 'Отчество',

            'dataRozhdeniya' => 'Дата рождения',

            'pasportNo' => 'Номер',
            'pasportKemVydanKod' => 'Кем выдан (код подразделения)',
            'pasportKemVydan' => 'Кем выдан',
            'pasportKogdaVydan' => 'Когда выдан',

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
            ['familiya', 'required'],
            ['familiya', ImyaChelovekaValidator::className()],
            ['imya', 'required'],
            ['imya', ImyaChelovekaValidator::className()],
            ['otchestvo', 'default'],
            ['otchestvo', ImyaChelovekaValidator::className()],

            ['dataRozhdeniya', 'required'],
            ['dataRozhdeniya', DateValidator::className(), 'sqlAttribute' => 'dataRozhdeniyaSql'],

            ['telefon', 'required'],
            ['telefon', TelefonValidator::className(), 'sqlAttribute' => 'telefonSql'],
            ['email', 'required'],
            ['email', EmailValidator::className()],

            ['pasportNo', 'required'],
            ['pasportNo', PasportNomerValidator::className(), 'sqlAttribute' => 'pasportNoSql'],
            ['pasportKemVydanKod', 'required'],
            ['pasportKemVydanKod', PasportKodPodrazdeleniyaValidator::className(), 'sqlAttribute' => 'pasportKemVydanKodSql'],
            ['pasportKemVydan', 'required'],
            ['pasportKemVydan', NazvanieValidator::className()],
            ['pasportKogdaVydan', 'required'],
            ['pasportKogdaVydan', DateValidator::className(), 'sqlAttribute' => 'pasportKogdaVydanSql'],

            ['inn', 'required'],
            ['inn', InnValidator::className(), 'sqlAttribute' => 'innSql'],
            ['snils', 'required'],
            ['snils', SnilsValidator::className(), 'sqlAttribute' => 'snilsSql'],

            ['propiska', 'required'],
            ['propiska', 'string']
        ];
    }

    public function populate()
    {
        /** @var $formatter Formatter */
        $formatter = Yii::$app->formatter;
        $formatter->nullDisplay = '';

        /** @var $fiz_lico FizLico */
        $fiz_lico = Yii::$app->user->fizLico;

        $this->familiya = $fiz_lico->familiya;
        $this->imya = $fiz_lico->imya;
        $this->otchestvo = $fiz_lico->otchestvo;

        $this->dataRozhdeniya = $formatter->asDate($fiz_lico->data_rozhdeniya);

        $this->telefon = $formatter->asTelefon($fiz_lico->telefon);
        $this->email = $fiz_lico->email;

        $this->pasportNo = $formatter->asPasportNomer($fiz_lico->pasport_no);
        $this->pasportKemVydan = $fiz_lico->pasport_kem_vydan;
        $this->pasportKemVydanKod = $formatter->asPasportKodPodrazdeleniya($fiz_lico->pasport_kem_vydan_kod);
        $this->pasportKogdaVydan = $formatter->asDate($fiz_lico->pasport_kogda_vydan);

        $this->inn = $formatter->asInn($fiz_lico->inn);
        $this->snils = $formatter->asSnils($fiz_lico->snils);

        $this->propiska = $fiz_lico->propiska;
    }

    public function save()
    {
        if (!$this->validate())
            return false;

        /**
         * @var $fiz_lico FizLico
         */
        $fiz_lico = Yii::$app->user->fizLico;

        $fiz_lico->familiya = $this->familiya;
        $fiz_lico->imya = $this->imya;
        $fiz_lico->otchestvo = $this->otchestvo;

        $fiz_lico->data_rozhdeniya = $this->dataRozhdeniyaSql;

        $fiz_lico->telefon = $this->telefonSql;
        $fiz_lico->email = $this->email;

        $fiz_lico->pasport_no = $this->pasportNoSql;
        $fiz_lico->pasport_kem_vydan = $this->pasportKemVydan;
        $fiz_lico->pasport_kem_vydan_kod = $this->pasportKemVydanKodSql;
        $fiz_lico->pasport_kogda_vydan = $this->pasportKogdaVydanSql;

        $fiz_lico->inn = $this->innSql;
        $fiz_lico->snils = $this->snilsSql;

        $fiz_lico->propiska = $this->propiska;

        $fiz_lico->save();

        return true;
    }
}