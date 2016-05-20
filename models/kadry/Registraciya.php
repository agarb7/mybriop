<?php
namespace app\models\kadry;

use app\entities\Dolzhnost;
use app\entities\settings\ZnachenieIdentifikatora;
use app\enums\EtapObrazovaniya;
use app\enums\Rol;
use app\entities\DolzhnostFizLicaNaRabote;
use app\entities\FizLico;
use app\entities\Organizaciya;
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
use app\components\captcha\CaptchaValidator;
use Yii;

class Registraciya extends Model
{
    public $familiya;
    public $imya;
    public $otchestvo;

    public $email;
    public $telefon;

    public $rabotaOrgAdres;
    public $rabotaOrgVedomstvo;
    public $rabotaOrgId;
    public $rabotaOrgNazvanie;

    public $rabotaDolzhnostId;
    public $rabotaDolzhnostNazvanie;

    public $rabotaEtapObrazovaniya;
    public $rabotaTelefon;

    public $login;
    public $parol;
    public $podtverzhdenieParolya;
    public $roli;

    public function setDefaultsIfEmpty()
    {
        if (array_filter($this->getAttributes()))
            return;

        $ids = ZnachenieIdentifikatora::get();

        $this->rabotaOrgAdres = $ids->gorodUlanUde;
        $this->rabotaOrgVedomstvo = $ids->vedomstvoMinobrnauki;
        $this->rabotaDolzhnostId = $ids->dolzhnostUchitel;
        $this->rabotaEtapObrazovaniya = EtapObrazovaniya::OSNOVNOE_OBSCHEE_OBRAZOVANIE;
        $this->roli = Rol::PEDAGOGICHESKIJ_RABOTNIK;
    }

    public function attributeLabels()
    {
        return [
            'familiya' => 'Фамилия',
            'imya' => 'Имя',
            'otchestvo' => 'Отчество',

            'email' => 'E-Mail',
            'telefon' => 'Личный телефон',

            'rabotaOrgAdres' => 'Район',
            'rabotaOrgVedomstvo' => 'Ведомство',
            'rabotaOrgId' => 'Образовательное учреждение',
            'rabotaOrgNazvanie' => 'Образовательное учреждение',

            'rabotaDolzhnostId' => 'Должность',
            'rabotaDolzhnostNazvanie' => 'Должность',

            'rabotaEtapObrazovaniya' => 'Уровень образования к которому относится ваша должность',
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

            [['rabotaOrgAdres', 'rabotaOrgVedomstvo'], 'integer'],
            [['rabotaOrgAdres', 'rabotaOrgVedomstvo'], 'required'],

            ['rabotaOrgId', 'integer'], //todo exists validator
            ['rabotaOrgId', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'rabotaOrgNazvanie'],
            ['rabotaOrgId', 'default'],

            ['rabotaOrgNazvanie', SqueezeLineFilter::className()],
            ['rabotaOrgNazvanie', NazvanieValidator::className()],
            ['rabotaOrgNazvanie', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'rabotaOrgId'],
            ['rabotaOrgNazvanie', 'default'],

            ['rabotaDolzhnostId', 'integer'], //todo exists validator
            ['rabotaDolzhnostId', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'rabotaDolzhnostNazvanie'],
            ['rabotaDolzhnostId', 'default'],

            ['rabotaDolzhnostNazvanie', SqueezeLineFilter::className()],
            ['rabotaDolzhnostNazvanie', NazvanieValidator::className()],
            ['rabotaDolzhnostNazvanie', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'rabotaDolzhnostId'],
            ['rabotaDolzhnostNazvanie', 'default'],

            ['rabotaEtapObrazovaniya', EnumValidator::className(), 'enumClass' => EtapObrazovaniya::className()],
            ['rabotaEtapObrazovaniya', 'default'],

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
        if (!$this->registerImpl()) {
            return false;
        }
        return true;
    }

    private function registerImpl()
    {
        if (!$this->validate())
            return false;

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

        list($organizaciya, $organizaciya_to_delete) = DirectoryHelper::getFromCombo(
            Organizaciya::className(),
            $this->rabotaOrgId,
            $this->rabotaOrgNazvanie,
            ['vedomstvo' => $this->rabotaOrgVedomstvo, 'adres_adresnyj_objekt' => $this->rabotaOrgAdres]
        );

        $rabota_fiz_lica = new RabotaFizLica([
            'formattedTelefon' => $this->rabotaTelefon
        ]);

        list($dolzhnost, $dolzhnost_to_delete) = DirectoryHelper::getFromCombo(
            Dolzhnost::className(),
            $this->rabotaDolzhnostId,
            $this->rabotaDolzhnostNazvanie,
            null
        );

        $dolzhnost_fiz_lica_na_rabote = new DolzhnostFizLicaNaRabote([
            'etapObrazovaniyaAsEnum' => $this->rabotaEtapObrazovaniya
        ]);

        $message = Yii::$app->mailer
            ->compose('kadry/registraciya.php', [
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

            if ($organizaciya)
                $organizaciya->save(false);

            $rabota_fiz_lica->fizLico = $fiz_lico->id;
            $rabota_fiz_lica->link('organizaciyaRel', $organizaciya);
            $rabota_fiz_lica->save(false);

            if ($dolzhnost)
                $dolzhnost->save(false);

            $dolzhnost_fiz_lica_na_rabote->dolzhnost = $dolzhnost->id;
            $dolzhnost_fiz_lica_na_rabote->link('rabotaFizLicaRel', $rabota_fiz_lica);
            $dolzhnost_fiz_lica_na_rabote->save(false);

            if ($organizaciya_to_delete)
                $organizaciya_to_delete->delete();

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
