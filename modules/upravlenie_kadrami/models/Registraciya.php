<?php
namespace app\modules\upravlenie_kadrami\models;

use app\entities\Dolzhnost;
use app\enums\EtapObrazovaniya;
use app\enums\Rol;
use app\entities\DolzhnostFizLicaNaRabote;
use app\entities\FizLico;
use app\entities\Polzovatel;
use app\entities\RabotaFizLica;
use app\helpers\DirectoryHelper;
use app\validators\EnumValidator;
use app\validators\ImyaChelovekaValidator;
use app\validators\LoginFilter;
use app\validators\LoginValidator;
use app\validators\NazvanieValidator;
use app\validators\RequiredWhenTargetIsEmpty;
use app\validators\SqueezeLineFilter;
use app\validators\TelefonValidator;
use yii\base\Model;
use app\enums2\OrgTipRaboty;
use Yii;

class Registraciya extends Model
{
    public $familiya;
    public $imya;
    public $otchestvo;

    public $email;
    public $telefon;

    public $rabotaTelefon;
    public $rabotaOrgTip;

    public $strukturnoePodrazdelenie;
    public $rukovoditelPodrazdeleniya;
    public $rabotaDolzhnostId;
    public $rabotaDolzhnostNazvanie;
    public $stazh;

    public $login;
    public $parol;
    public $podtverzhdenieParolya;
    public $roli;

    public function setDefaults()
    {
        if (array_filter($this->getAttributes()))
            return;

        $this->roli = [Rol::RUKOVODITEL_KURSOV];
    }

    public function attributeLabels()
    {
        return [
            'familiya' => 'Фамилия',
            'imya' => 'Имя',
            'otchestvo' => 'Отчество',

            'email' => 'E-Mail',
            'telefon' => 'Личный телефон',

            'rabotaOrgTip' => 'Тип занятости',

            'strukturnoePodrazdelenie' => 'Структурное подразделение',
            'rukovoditelPodrazdeleniya' => 'Руководитель подразделения',
            'stazh' => 'Стаж в должности',

            'rabotaDolzhnostId' => 'Должность',
            'rabotaDolzhnostNazvanie' => 'Должность',
            'rabotaTelefon' => 'Рабочий телефон',

            'login' => 'Логин',
            'parol' => 'Пароль',
            'podtverzhdenieParolya' => 'Подтверждение пароля',
            'roli' => 'Роли пользователя',
        ];
    }

    public function attributeHints()
    {
        return [
            'login' => 'Логин может состоять из латинских букв и/или из цифр. Примеры: abc123, 12345, 21abc',
        ];
    }

    public function rules()
    {
        return [
            [['familiya','imya', 'otchestvo'], SqueezeLineFilter::className()],
            [['familiya','imya', 'otchestvo'], ImyaChelovekaValidator::className()],
            [['familiya','imya'], 'required'],
            ['otchestvo', 'default'],

            ['email', 'email'],
            ['email', 'required'],

            ['telefon', TelefonValidator::className()],
            ['telefon', 'default'],

            ['rabotaOrgTip', EnumValidator::className(), 'enumClass' => OrgTipRaboty::className()],

            [['strukturnoePodrazdelenie', 'rukovoditelPodrazdeleniya'], 'required'],
            ['strukturnoePodrazdelenie', 'integer'],
            ['rukovoditelPodrazdeleniya', 'boolean'],
            ['stazh', 'integer'],

            ['rabotaDolzhnostId', 'integer'], //todo exists validator
            ['rabotaDolzhnostId', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'rabotaDolzhnostNazvanie'],
            ['rabotaDolzhnostId', 'default'],

            ['rabotaDolzhnostNazvanie', SqueezeLineFilter::className()],
            ['rabotaDolzhnostNazvanie', NazvanieValidator::className()],
            ['rabotaDolzhnostNazvanie', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'rabotaDolzhnostId'],
            ['rabotaDolzhnostNazvanie', 'default'],

            ['rabotaTelefon', TelefonValidator::className()],
            ['rabotaTelefon', 'default'],

            ['login', LoginFilter::className()],
            ['login', LoginValidator::className()],
            ['login', 'unique', 'targetClass' => Polzovatel::className(), 'targetAttribute' => 'login'], //todo ajax
            ['login', 'required'],

            ['podtverzhdenieParolya', 'compare', 'compareAttribute' => 'parol', 'message' => 'Пароль должен совпадать с подтверждением пароля.'],
            [['parol', 'podtverzhdenieParolya'], 'required'],
            [['roli'], 'required'],
        ];
    }

    public function register()
    {
        if (!$this->validate())
            return false;

        $organizaciya = 1;

        $fiz_lico = new FizLico([
            'familiya' => $this->familiya,
            'imya' => $this->imya,
            'otchestvo' => $this->otchestvo,
            'email' => $this->email,
            'formattedTelefon' => $this->telefon
        ]);

        $polzovatel = new Polzovatel([
            'login' => $this->login,
            'parol' => $this->parol,
            'aktiven' => true,
            'roliAsArray' => $this->roli,
        ]);

        $polzovatel->generateKlyuchAutentifikacii();
        $polzovatel->generateKodPodtverzhdeniyaEmail();

        $rabota_fiz_lica = new RabotaFizLica([
            'formattedTelefon' => $this->rabotaTelefon,
            'org_tip' => $this->rabotaOrgTip,
        ]);

        list($dolzhnost, $dolzhnost_to_delete) = DirectoryHelper::getFromCombo(
            Dolzhnost::className(),
            $this->rabotaDolzhnostId,
            $this->rabotaDolzhnostNazvanie,
            null
        );

        $dolzhnost_fiz_lica_na_rabote = new DolzhnostFizLicaNaRabote([
            'etapObrazovaniyaAsEnum' => EtapObrazovaniya::DOPOLNITELNOE_OBRAZOVANIE,
            'strukturnoe_podrazdelenie' => $this->strukturnoePodrazdelenie,
            'rukovoditel_strukturnogo_podrazdeleniya' => $this->rukovoditelPodrazdeleniya,
            'stazh' => $this->stazh,
            'actual' => true,
        ]);

        $message = Yii::$app->mailer
            ->compose('upravlenie-kadrami/registraciya', [
                'model' => $this,
                'polzovatel' => $polzovatel
            ])
            ->setTo($fiz_lico->email);

        if (!$message->send()) {
            $this->addError('email', 'Не удалось отправить E-Mail.');
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $fiz_lico->save(false);
            $polzovatel->link('fizLicoRel', $fiz_lico);
            $polzovatel->save(false);

            $rabota_fiz_lica->fizLico = $fiz_lico->id;
            $rabota_fiz_lica->organizaciya = $organizaciya;
            $rabota_fiz_lica->save(false);

            if ($dolzhnost)
                $dolzhnost->save(false);

            $dolzhnost_fiz_lica_na_rabote->dolzhnost = $dolzhnost->id;
            $dolzhnost_fiz_lica_na_rabote->link('rabotaFizLicaRel', $rabota_fiz_lica);
            $dolzhnost_fiz_lica_na_rabote->save(false);

            if ($dolzhnost_to_delete)
                $dolzhnost_to_delete->delete();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }
}
