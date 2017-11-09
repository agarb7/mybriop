<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 24.10.2017
 * Time: 20:18
 */

namespace app\modules\upravlenie_kadrami\models;

use app\enums2\TipDogovoraRaboty;
use yii\base\Model;
use app\enums2\Rol;
use app\entities\Dolzhnost;
use app\enums\EtapObrazovaniya;
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
use app\enums2\OrgTipRaboty;
use Yii;
use yii\validators\Validator;

/**
 * Class Sotrudnik
 * @package app\modules\upravlenie_kadrami\models
 */
class Sotrudnik extends Model
{
    public $familiya;
    public $imya;
    public $otchestvo;
    public $fio;
    public $fizLicoId;
    public $email;
    public $telefon;

    public $rabotaTelefon;
    public $rabotaOrgTip;
    public $rabotaDolyaStavki;
    public $tipDogovora;
    public $rabotaId;

    public $strukturnoePodrazdelenie;
    public $rukovoditelPodrazdeleniya;
    public $rabotaDolzhnostId;
    public $rabotaDolzhnostNazvanie;
    public $stazh;
    public $actual;
    public $dolzhnostFizLicaNaRaboteId;

    public $login;
    public $parol;
    public $podtverzhdenieParolya;
    public $roli;
    public $polzovatelId;
    
    public function __construct($fl = null, $dflnr = null)
    {
        parent::__construct();
        if ($fl == null or $dflnr == null) return;
        else{
            $this->fizLicoId = $fl;
            $fizLico = FizLico::findOne(['id' => $this->fizLicoId]);
            $this->familiya = $fizLico->familiya;
            $this->imya = $fizLico->imya;
            $this->otchestvo = $fizLico->otchestvo;
            $this->fio = $fizLico->getFio();
            $this->email = $fizLico->email;
            $this->telefon = $fizLico->formattedTelefon;

            $polzovatel = Polzovatel::findOne(['fiz_lico' => $this->fizLicoId]);
            $this->login = $polzovatel->login;
            $this->roli = $polzovatel->roliAsArray;
            $this->polzovatelId = $polzovatel->id;

            $dolzhnostFizLicaNaRabote = DolzhnostFizLicaNaRabote::find()->with('dolzhnostRel')->where(['id' => $dflnr])->one();
            $this->dolzhnostFizLicaNaRaboteId = $dflnr;
            $this->rabotaId = $dolzhnostFizLicaNaRabote->rabota_fiz_lica;
            $this->strukturnoePodrazdelenie = $dolzhnostFizLicaNaRabote->strukturnoe_podrazdelenie;
            $this->rukovoditelPodrazdeleniya = $dolzhnostFizLicaNaRabote->rukovoditel_strukturnogo_podrazdeleniya;
            $this->rabotaDolzhnostId = $dolzhnostFizLicaNaRabote->dolzhnost;
            $this->stazh = $dolzhnostFizLicaNaRabote->stazh;

            $rabotaFizLica = RabotaFizLica::findOne(['id' => $this->rabotaId]);
            $this->rabotaOrgTip = $rabotaFizLica->org_tip;
            $this->rabotaDolyaStavki = $rabotaFizLica->dolya_stavki;
            $this->tipDogovora = $rabotaFizLica->tip_dogovora;
            $this->rabotaTelefon = $rabotaFizLica->telefon;
        }
    }

    public function attributeLabels()
    {
        return [
            'familiya' => 'Фамилия',
            'imya' => 'Имя',
            'otchestvo' => 'Отчество',

            'email' => 'E-Mail',
            'telefon' => 'Личный телефон',

            'tipDogovora' => 'Тип договора',
            'rabotaOrgTip' => 'Вид занятости',
            'rabotaDolyaStavki' => 'Доля ставки',

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

    public function setDefaults()
    {
        if (array_filter($this->getAttributes()))
            return;

        $this->roli = [Rol::RUKOVODITEL_KURSOV];
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
            ['fizLicoId', 'integer'],

            ['email', 'email'],
            ['email', 'required'],

            ['telefon', TelefonValidator::className()],
            ['telefon', 'default'],

            ['tipDogovora', EnumValidator::className(), 'enumClass' => TipDogovoraRaboty::className()],
            ['rabotaOrgTip', EnumValidator::className(), 'enumClass' => OrgTipRaboty::className()],

            ['rabotaDolyaStavki', 'required',
                'when' => function(){return $this->tipDogovora == 'trud';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#sotrudnik-tipdogovora').val() == 'trud';
                }"
            ],
            ['rabotaDolyaStavki', 'default'],

            [['strukturnoePodrazdelenie', 'rukovoditelPodrazdeleniya'], 'required'],
            ['strukturnoePodrazdelenie', 'integer'],
            ['rukovoditelPodrazdeleniya', 'boolean'],
            ['stazh', 'integer'],

            ['rabotaDolzhnostId', 'integer'], //todo exists validator
            ['rabotaDolzhnostId', 'required',
                'when' => function(){
                    $v=new Validator();
                    return $v->isEmpty(is_string($this->rabotaDolzhnostNazvanie) ? trim($this->rabotaDolzhnostNazvanie) : $this->rabotaDolzhnostNazvanie) && $this->tipDogovora == 'trud';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#sotrudnik-tipdogovora').val() == 'trud';
                }"
            ],
            ['rabotaDolzhnostId', 'default'],

            ['rabotaDolzhnostNazvanie', SqueezeLineFilter::className()],
            ['rabotaDolzhnostNazvanie', NazvanieValidator::className()],
            ['rabotaDolzhnostNazvanie', 'required',
                'when' => function() {
                    $v=new Validator();
                    return $v->isEmpty($this->rabotaDolzhnostId) && $this->tipDogovora == 'trud';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#sotrudnik-tipdogovora').val() == 'trud';
                }"
            ],
            ['rabotaDolzhnostNazvanie', 'default'],

            ['rabotaTelefon', TelefonValidator::className()],
            ['rabotaTelefon', 'default'],
            ['rabotaId', 'integer'],
            ['dolzhnostFizLicaNaRaboteId', 'integer'],

            ['login', LoginFilter::className()],
            ['login', LoginValidator::className()],
            ['login', 'unique', 'targetClass' => Polzovatel::className(), 'targetAttribute' => 'login'], //todo ajax
            ['login', 'required'],
            ['polzovatelId', 'integer'],

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
            'tip_dogovora' => $this->tipDogovora,
            'organizaciya' => $organizaciya,
        ]);

        $dolzhnost_fiz_lica_na_rabote = new DolzhnostFizLicaNaRabote([
            'etapObrazovaniyaAsEnum' => EtapObrazovaniya::DOPOLNITELNOE_OBRAZOVANIE,
            'strukturnoe_podrazdelenie' => $this->strukturnoePodrazdelenie,
            'actual' => true,
        ]);

        if ($this->tipDogovora <> 'gph') {
            $rabota_fiz_lica->formattedTelefon = $this->rabotaTelefon;
            $rabota_fiz_lica->org_tip = $this->rabotaOrgTip;
            $rabota_fiz_lica->dolya_stavki = $this->rabotaDolyaStavki;

            $dolzhnost_fiz_lica_na_rabote->rukovoditel_strukturnogo_podrazdeleniya = $this->rukovoditelPodrazdeleniya;
            $dolzhnost_fiz_lica_na_rabote->stazh = $this->stazh;
        }

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

        list($dolzhnost, $dolzhnost_to_delete) = DirectoryHelper::getFromCombo(
            Dolzhnost::className(),
            $this->rabotaDolzhnostId,
            $this->rabotaDolzhnostNazvanie,
            null
        );

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $fiz_lico->save(false);
            $polzovatel->link('fizLicoRel', $fiz_lico);
            $polzovatel->save(false);

            $rabota_fiz_lica->fizLico = $fiz_lico->id;
            $rabota_fiz_lica->save(false);

            if ($dolzhnost) {
                $dolzhnost->obschij = true;
                if (!$dolzhnost->save(false)) $e = true;
                $dolzhnost_fiz_lica_na_rabote->dolzhnost = $dolzhnost->id;
            }

            $dolzhnost_fiz_lica_na_rabote->link('rabotaFizLicaRel', $rabota_fiz_lica);
            $dolzhnost_fiz_lica_na_rabote->save(false);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }

    public function editImpl()
    {
        $fiz_lico = FizLico::findOne(['id' => $this->fizLicoId]);
        $fiz_lico->imya = $this->imya;
        $fiz_lico->otchestvo = $this->otchestvo;
        $fiz_lico->email = $this->email;
        $fiz_lico->formattedTelefon = $this->telefon;

        $polzovatel = Polzovatel::findOne(['id' => $this->polzovatelId]);
        $polzovatel->login = $this->login;
        $polzovatel->roliAsArray = $this->roli;

        $rabota_fiz_lica = RabotaFizLica::findOne(['id' => $this->rabotaId]);
        $rabota_fiz_lica->tip_dogovora = $this->tipDogovora;

        $dflnr = DolzhnostFizLicaNaRabote::findOne(['id' => $this->dolzhnostFizLicaNaRaboteId]);
        $dflnr->strukturnoe_podrazdelenie = $this->strukturnoePodrazdelenie;
        $dflnr->rukovoditel_strukturnogo_podrazdeleniya = $this->rukovoditelPodrazdeleniya;

        if ($this->tipDogovora == 'gph') {
            $rabota_fiz_lica->dolya_stavki = null;
            $rabota_fiz_lica->org_tip = null;
            $rabota_fiz_lica->telefon = null;
            $dflnr->dolzhnost = null;
            $dflnr->stazh = null;
        } else {
            $rabota_fiz_lica->dolya_stavki = $this->rabotaDolyaStavki;
            $rabota_fiz_lica->org_tip = $this->rabotaOrgTip;
            $rabota_fiz_lica->formattedTelefon = $this->rabotaTelefon;
            $dflnr->stazh = $this->stazh;
        }

        list($dolzhnost, $dolzhnost_to_delete) = DirectoryHelper::getFromCombo(
            Dolzhnost::className(),
            $this->rabotaDolzhnostId,
            $this->rabotaDolzhnostNazvanie,
            null
        );

        $e = false;
        $transaction = \Yii::$app->db->beginTransaction();
        if (!$fiz_lico->save(false)) $e = true;
        if (!$polzovatel->save(false)) $e = true;

        if ($dolzhnost && $this->tipDogovora == 'trud') {
            $dolzhnost->obschij = true;
            if (!$dolzhnost->save(false)) $e = true;
            $dflnr->dolzhnost = $dolzhnost->id;
        }

        if (!$rabota_fiz_lica->save(false)) $e = true;

        if (!$dflnr->save(false)) $e = true;

        if (!$e) {
            $transaction->commit();
            return true;
        }else{
            $transaction->rollback();
            return false;
        }
    }

    public function sovmeshenieImpl()
    {
        $new_rfl = new RabotaFizLica();
        $new_rfl->fiz_lico = $this->fizLicoId;
        $new_rfl->tip_dogovora = $this->tipDogovora;
        $new_rfl->org_tip = $this->rabotaOrgTip;
        $new_rfl->dolya_stavki = $this->rabotaDolyaStavki;
        $new_rfl->organizaciya = 1;
        $new_rfl->formattedTelefon = $this->rabotaTelefon;

        list($dolzhnost, $dolzhnost_to_delete) = DirectoryHelper::getFromCombo(
            Dolzhnost::className(),
            $this->rabotaDolzhnostId,
            $this->rabotaDolzhnostNazvanie,
            null
        );

        $new_dflnr = new DolzhnostFizLicaNaRabote();
        $new_dflnr->strukturnoe_podrazdelenie = $this->strukturnoePodrazdelenie;
        $new_dflnr->dolzhnost = $dolzhnost->id;
        $new_dflnr->rukovoditel_strukturnogo_podrazdeleniya = $this->rukovoditelPodrazdeleniya;
        $new_dflnr->stazh = $this->stazh;

        $e = false;
        $transaction = \Yii::$app->db->beginTransaction();
        if ($dolzhnost) {
            $dolzhnost->obschij = true;
            if (!$dolzhnost->save(false)) $e = true;
        }

        if (!$new_rfl->save(false)) $e = true;
        $new_dflnr->rabota_fiz_lica = $new_rfl->id;

        if (!$new_dflnr->save(false)) $e = true;

        if (!$e) {
            $transaction->commit();
            return true;
        }else{
            $transaction->rollback();
            return false;
        }
    }

    public function perevodImpl()
    {
        $dflnr = DolzhnostFizLicaNaRabote::findOne(['id' => $this->dolzhnostFizLicaNaRaboteId]);
        $dflnr->actual = false;

        if ($this->sovmeshenieImpl() && $dflnr->save(false)) return true;
            else return false;
    }
}