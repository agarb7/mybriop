<?php
namespace app\models\kurs_slushatelyu;

use app\entities\AttestaciyaFizLica;
use app\entities\Dolzhnost;
use app\entities\DolzhnostFizLicaNaRabote;
use app\entities\FizLico;
use app\entities\Kurs;
use app\entities\KursFizLica;
use app\entities\Kvalifikaciya;
use app\entities\ObrazovanieFizLica;
use app\entities\Organizaciya;
use app\entities\RabotaFizLica;
use app\entities\StazhFizLica;
use app\entities\TekuschayaAttestaciyaFizLica;
use app\enums\KategoriyaPedRabotnika;
use app\enums\StatusZapisiNaKurs;
use app\enums\TipDokumentaObObrazovanii;
use app\helpers\DirectoryHelper;
use app\helpers\StringHelper;
use app\helpers\Val;
use app\validators\EnumValidator;
use app\validators\InnValidator;
use app\validators\NazvanieValidator;
use app\validators\NomerDokumentaValidator;
use app\validators\PasportKodPodrazdeleniyaValidator;
use app\validators\PasportNomerValidator;
use app\validators\RequiredWhenTargetIsEmpty;
use app\validators\SnilsValidator;
use app\validators\SqueezeLineFilter;
use app\validators\StazhValidator;
use app\widgets\DeprecatedDatePicker;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use Yii;

class ZapisNaKursForm extends Model
{
    public $fizLico;
    public $kurs;

    public $pedStazh;
    public $stazhVDolzhnosti;
    public $kategoriya;

    public $dolzhnostId;
    public $dolzhnostNazvanie;

    public $obrOrgId;
    public $obrOrgNazvanie;

    public $obrKvalifikaciyaId;
    public $obrKvalifikaciyaNazvanie;

    public $obrDocTip;
    public $obrDocSeriya;
    public $obrDocNomer;
    public $obrDocData;

    public $pasportNomer;
    public $pasportNomerSql;
    public $pasportKemVydanKod;
    public $pasportKemVydan;
    public $pasportKogdaVydan;

    public $propiska;

    public $dataRozhdeniya;
    public $snils;
    public $inn;

    const SCENARIO_ZAPIS_BYUDZHET = 'zapis_byudzhet';
    const SCENARIO_ZAPIS_VNEBYUDZHET = 'zapis_vnebyudzhet';
    const SCENARIO_OTMENA_ZAPISI = 'otmena_zapisi';

    public function attributeLabels()
    {
        return [
            'fizLico' => 'Физ. лицо',
            'kurs' => 'Курс',

            'pedStazh' => 'Общий педагогический стаж',
            'stazhVDolzhnosti' => 'Стаж в занимаемой должности',
            'kategoriya' => 'Категория',

            'dolzhnostId' => 'Должность',
            'dolzhnostNazvanie' => 'Должность',

            'obrOrgId' => 'Образовательная организация',
            'obrOrgNazvanie' => 'Образовательная организация',

            'obrKvalifikaciyaId' => 'Квалификация',
            'obrKvalifikaciyaNazvanie' => 'Квалификация',

            'obrDocTip' => 'Тип',
            'obrDocSeriya' => 'Серия',
            'obrDocNomer' => 'Номер',
            'obrDocData' => 'Дата выдачи',

            'pasportSeriya' => 'Серия',
            'pasportNomer' => 'Номер',
            'pasportKemVydanKod' => 'Код подразделения',
            'pasportKemVydan' => 'Кем выдан',
            'pasportKogdaVydan' => 'Дата выдачи',

            'propiska' => 'Прописка',

            'dataRozhdeniya' => 'Дата рождения',
            'snils' => 'СНИЛС',
            'inn' => 'ИНН'
        ];
    }

    public function rules()
    {
        return [
            ['fizLico', 'exist', 'targetAttribute' => 'id', 'targetClass' => FizLico::className()],
            ['fizLico', 'required'],

            ['kurs', 'exist',  'targetAttribute' => 'id', 'targetClass' => Kurs::className()],
            ['kurs', 'required'],

            ['pedStazh', StazhValidator::className()],
            ['pedStazh', 'required'],

            ['stazhVDolzhnosti', StazhValidator::className()],
            ['stazhVDolzhnosti', 'required'],

            ['kategoriya', EnumValidator::className(), 'enumClass' => KategoriyaPedRabotnika::className()],
            ['kategoriya', 'required'],


            ['dolzhnostId', 'integer'],
            ['dolzhnostId', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'dolzhnostNazvanie'],

            ['dolzhnostNazvanie', SqueezeLineFilter::className()],
            ['dolzhnostNazvanie', NazvanieValidator::className()],
            ['dolzhnostNazvanie', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'dolzhnostId'],
            ['dolzhnostNazvanie', 'default'],


            ['obrOrgId', 'integer'],
            ['obrOrgId', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'obrOrgNazvanie'],

            ['obrOrgNazvanie', SqueezeLineFilter::className()],
            ['obrOrgNazvanie', NazvanieValidator::className()],
            ['obrOrgNazvanie', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'obrOrgId'],
            ['obrOrgNazvanie', 'default'],


            ['obrKvalifikaciyaId', 'integer'],
            ['obrKvalifikaciyaId', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'obrKvalifikaciyaNazvanie'],

            ['obrKvalifikaciyaNazvanie', SqueezeLineFilter::className()],
            ['obrKvalifikaciyaNazvanie', NazvanieValidator::className()],
            ['obrKvalifikaciyaNazvanie', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'obrKvalifikaciyaId'],
            ['obrKvalifikaciyaNazvanie', 'default'],


            ['obrDocTip', EnumValidator::className(), 'enumClass' => TipDokumentaObObrazovanii::className()],
            ['obrDocTip', 'required'],

            ['obrDocSeriya', SqueezeLineFilter::className()],
            ['obrDocSeriya', NomerDokumentaValidator::className()],
            ['obrDocSeriya', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'obrDocNomer'],
            ['obrDocSeriya', 'default'],

            ['obrDocNomer', SqueezeLineFilter::className()],
            ['obrDocNomer', NomerDokumentaValidator::className()],
            ['obrDocNomer', RequiredWhenTargetIsEmpty::className(), 'targetModel'=>$this, 'targetAttribute'=>'obrDocSeriya'],
            ['obrDocNomer', 'default'],

            ['obrDocData', 'date'],
            ['obrDocData', 'required'],

            ['pasportNomer', PasportNomerValidator::className(), 'sqlAttribute' => 'pasportNomerSql'],
            ['pasportNomer', 'required'],

            ['pasportKemVydanKod', PasportKodPodrazdeleniyaValidator::className()],
            ['pasportKemVydanKod', 'required'],

            ['pasportKemVydan', SqueezeLineFilter::className()],
            ['pasportKemVydan', NazvanieValidator::className()],
            ['pasportKemVydan', 'required'],

            ['pasportKogdaVydan', 'date'],
            ['pasportKogdaVydan', 'required'],

            ['propiska', SqueezeLineFilter::className()],
            ['propiska', NazvanieValidator::className()],
            ['propiska', 'required'],

            ['dataRozhdeniya', 'date'],
            ['dataRozhdeniya', 'required'],

            ['snils', SnilsValidator::className()],
            ['snils', 'required'],

            ['inn', InnValidator::className()],
            ['inn', 'required']
        ];
    }

    public function scenarios()
    {
        $otm_attrs = ['fizLico', 'kurs'];

        $byud_attrs = ArrayHelper::merge($otm_attrs, [
            'pedStazh',
            'stazhVDolzhnosti',
            'kategoriya',

            'dolzhnostId',
            'dolzhnostNazvanie',

            'obrOrgId',
            'obrOrgNazvanie',

            'obrKvalifikaciyaId',
            'obrKvalifikaciyaNazvanie',

            'obrDocTip',
            'obrDocSeriya',
            'obrDocNomer',
            'obrDocData'
        ]);

        $vnebyud_attrs = ArrayHelper::merge($byud_attrs, [
            'pasportNomer',
            'pasportKemVydanKod',
            'pasportKemVydan',
            'pasportKogdaVydan',

            'propiska',

            'dataRozhdeniya',
            'snils',
            'inn'
        ]);

        return [
            self::SCENARIO_OTMENA_ZAPISI => $otm_attrs,
            self::SCENARIO_ZAPIS_BYUDZHET => $byud_attrs,
            self::SCENARIO_ZAPIS_VNEBYUDZHET => $vnebyud_attrs
        ];
    }

    public function populateByudzhet()
    {
        $this->populateByudzhetImpl();
    }

    public function populateVnebyudzhet()
    {
        $fiz_lico = $this->populateByudzhetImpl();

        if ($fiz_lico) {
            $this->pasportNomer = Yii::$app->formatter->asPasportNomer($fiz_lico->pasport_no);
            $this->pasportKemVydanKod = $fiz_lico->pasportKemVydanKodFormatted;
            $this->pasportKemVydan = $fiz_lico->pasportKemVydan;
            $this->pasportKogdaVydan = DeprecatedDatePicker::fromDatetime($fiz_lico->pasportKogdaVydanAsDate);

            $this->propiska = $fiz_lico->propiska;

            $this->dataRozhdeniya = DeprecatedDatePicker::fromDatetime($fiz_lico->dataRozhdeniyaAsDate);
            $this->snils = $fiz_lico->snilsFormatted;
            $this->inn = $fiz_lico->inn;
        }
    }

    public function otmenitZapis($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames))
            return false;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->saveKursFizLica(StatusZapisiNaKurs::OTMENA_ZAPISI);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }

    public function zapisatsyaByudzhet($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames))
            return false;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            list($fiz_lico, $dolzhnost_fiz_lica_na_rabote) = $this->saveByudzhetZapisEntities();
            $this->saveKursFizLica(StatusZapisiNaKurs::ZAPIS, Val::of($dolzhnost_fiz_lica_na_rabote,'id'));

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->sendSuccessfulEmail($fiz_lico, $this->kurs);

        return true;
    }

    public function zapisatsyaVnebyudzhet($runValidation = true, $attributeNames = null)
    {
        if ($runValidation && !$this->validate($attributeNames))
            return false;

        $transaction = Yii::$app->db->beginTransaction();
        try {
            list($fiz_lico, $dolzhnost_fiz_lica_na_rabote) = $this->saveByudzhetZapisEntities();

            $fiz_lico->pasportNo = $this->pasportNomerSql;
            $fiz_lico->pasportKemVydanKodFormatted = $this->pasportKemVydanKod;
            $fiz_lico->pasportKemVydan = $this->pasportKemVydan;
            $fiz_lico->pasportKogdaVydanAsDate = DeprecatedDatePicker::toDatetime($this->pasportKogdaVydan);

            $fiz_lico->propiska = $this->propiska;

            $fiz_lico->dataRozhdeniyaAsDate = DeprecatedDatePicker::toDatetime($this->dataRozhdeniya);
            $fiz_lico->snilsFormatted = $this->snils;
            $fiz_lico->inn = $this->inn;

            $fiz_lico->save(false);

            $this->saveKursFizLica(StatusZapisiNaKurs::ZAPIS, Val::of($dolzhnost_fiz_lica_na_rabote,'id'));

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        $this->sendSuccessfulEmail($fiz_lico, $this->kurs);

        return true;
    }

    private function saveKursFizLica($status, $dolzhnostNaRaboteId=null)
    {
        $link = ['fiz_lico' => $this->fizLico, 'kurs' => $this->kurs];

        $kurs_fiz_lica = KursFizLica::findOne($link) ?: new KursFizLica($link);
        $kurs_fiz_lica->status = $status;
        $kurs_fiz_lica->dolzhnostFizLicaNaRabote = $dolzhnostNaRaboteId;
        $kurs_fiz_lica->vremyaSmenyStatusaAsDatetime = new \DateTime();

        $kurs_fiz_lica->save(false);
    }

    /**
     * @return FizLico
     */
    private function saveByudzhetZapisEntities()
    {
        $fiz_lico = FizLico::findOne($this->fizLico);
        $fiz_lico->pedStazh = $this->pedStazh;

        $fiz_lico->save(false);

        $rabota_fiz_lica_conf = ['fiz_lico' => $this->fizLico];
        $rabota_fiz_lica
            = RabotaFizLica::find()->where($rabota_fiz_lica_conf)->orderBy('id')->one()
            ?: new RabotaFizLica($rabota_fiz_lica_conf);

        $rabota_fiz_lica->save(false);


        $dolzhnost_fiz_lica_na_rabote_conf = ['rabota_fiz_lica' => $rabota_fiz_lica->id];
        $dolzhnost_fiz_lica_na_rabote
            = DolzhnostFizLicaNaRabote::find()->where($dolzhnost_fiz_lica_na_rabote_conf)->orderBy('id')->one()
            ?: new DolzhnostFizLicaNaRabote($dolzhnost_fiz_lica_na_rabote_conf);

        list($dolzhnost, $dolzhnost_to_delete) = DirectoryHelper::getFromCombo(
            Dolzhnost::className(),
            $this->dolzhnostId,
            $this->dolzhnostNazvanie,
            $dolzhnost_fiz_lica_na_rabote->dolzhnost
        );

        if ($dolzhnost)
            $dolzhnost->save(false);

        $dolzhnost_fiz_lica_na_rabote->link('dolzhnostRel', $dolzhnost);
        $dolzhnost_fiz_lica_na_rabote->save(false);

//        if ($dolzhnost_to_delete)
//            $dolzhnost_to_delete->delete();


        $stazh_v_dolzhnosti_conf = ['fiz_lico' => $this->fizLico, 'dolzhnost' => $dolzhnost->id];
        $stazh_v_dolzhnosti
            = StazhFizLica::findOne($stazh_v_dolzhnosti_conf)
            ?: new StazhFizLica($stazh_v_dolzhnosti_conf);

        $stazh_v_dolzhnosti->stazh = $this->stazhVDolzhnosti;

        $stazh_v_dolzhnosti->save(false);


        // todo refactor
        $attestaciya_fiz_lica = $fiz_lico->attestaciyaFizLicaRel;
        if(!$attestaciya_fiz_lica) {
            $attestaciya_fiz_lica = new AttestaciyaFizLica(['fiz_lico' => $this->fizLico]);
            $attestaciya_fiz_lica->save(false);

            $tekuschaya_attestaciya_fiz_lica = new TekuschayaAttestaciyaFizLica([
                'fiz_lico' => $this->fizLico,
                'attestaciya_fiz_lica' => $attestaciya_fiz_lica->id
            ]);

            $tekuschaya_attestaciya_fiz_lica->save(false);
        }

        $attestaciya_fiz_lica->kategoriya = $this->kategoriya;

        $attestaciya_fiz_lica->save(false);


        $obrazovanie_fiz_lica_conf = ['fiz_lico' => $this->fizLico];
        $obrazovanie_fiz_lica
            = ObrazovanieFizLica::find()->where($obrazovanie_fiz_lica_conf)->orderBy('id')->one()
            ?: new ObrazovanieFizLica($obrazovanie_fiz_lica_conf);

        $obrazovanie_fiz_lica->dokumentObObrazovaniiTip = $this->obrDocTip;
        $obrazovanie_fiz_lica->dokumentObObrazovaniiSeriya = $this->obrDocSeriya;
        $obrazovanie_fiz_lica->dokumentObObrazovaniiNomer = $this->obrDocNomer;
        $obrazovanie_fiz_lica->dokumentObObrazovaniiDataAsDate = DeprecatedDatePicker::toDatetime($this->obrDocData);

        list($organizaciya, $organizaciya_to_delete) = DirectoryHelper::getFromCombo(
            Organizaciya::className(),
            $this->obrOrgId,
            $this->obrOrgNazvanie,
            $obrazovanie_fiz_lica->organizaciya,
            ['etapyObrazovaniya' => '{vp}']
        );

        if ($organizaciya)
            $organizaciya->save(false);

        list($kvalifikaciya, $kvalifikaciya_to_delete) = DirectoryHelper::getFromCombo(
            Kvalifikaciya::className(),
            $this->obrKvalifikaciyaId,
            $this->obrKvalifikaciyaNazvanie,
            $obrazovanie_fiz_lica->kvalifikaciya
        );

        if ($kvalifikaciya)
            $kvalifikaciya->save(false);

        $obrazovanie_fiz_lica->link('organizaciyaRel', $organizaciya);
        $obrazovanie_fiz_lica->link('kvalifikaciyaRel', $kvalifikaciya);
        $obrazovanie_fiz_lica->save(false);

        if ($organizaciya_to_delete)
            $organizaciya_to_delete->delete();

        if ($kvalifikaciya_to_delete)
            $kvalifikaciya_to_delete->delete();

        return [$fiz_lico, $dolzhnost_fiz_lica_na_rabote];
    }

    private function populateByudzhetImpl()
    {
        $fiz_lico = FizLico::findOne($this->fizLico);

        if ($fiz_lico)
            $this->pedStazh = $fiz_lico->pedStazh;

        $rabota_fiz_lica = RabotaFizLica::find()->where(['fiz_lico' => $this->fizLico])->orderBy('id')->one();
        $dolzhnost_fiz_lica_na_rabote = $rabota_fiz_lica
            ? DolzhnostFizLicaNaRabote::find()->where(['rabota_fiz_lica' => $rabota_fiz_lica->id])->orderBy('id')->one()
            : null;

        if ($dolzhnost_fiz_lica_na_rabote) {
            list($this->dolzhnostId, $this->dolzhnostNazvanie) = DirectoryHelper::getForCombo(
                Dolzhnost::findOne($dolzhnost_fiz_lica_na_rabote->dolzhnost)
            );
        }

        if ($dolzhnost_fiz_lica_na_rabote && $stazh_v_dolzhnosti = StazhFizLica::findOne([
            'fiz_lico' => $this->fizLico, 'dolzhnost' => Val::of($dolzhnost_fiz_lica_na_rabote, 'dolzhnost')
        ]))
        {
            $this->stazhVDolzhnosti = $stazh_v_dolzhnosti->stazh;
        }

        if ($attestaciya_fiz_lica = $fiz_lico->attestaciyaFizLicaRel)
            $this->kategoriya = $attestaciya_fiz_lica->kategoriya;

        if ($obrazovanie_fiz_lica = ObrazovanieFizLica::find()->where(['fiz_lico' => $this->fizLico])->orderBy('id')->one()) {
            list($this->obrOrgId, $this->obrOrgNazvanie) = DirectoryHelper::getForCombo(
                Organizaciya::findOne($obrazovanie_fiz_lica->organizaciya)
            );

            list($this->obrKvalifikaciyaId, $this->obrKvalifikaciyaNazvanie) = DirectoryHelper::getForCombo(
                Kvalifikaciya::findOne($obrazovanie_fiz_lica->kvalifikaciya)
            );

            $this->obrDocTip = $obrazovanie_fiz_lica->dokumentObObrazovaniiTip;
            $this->obrDocSeriya = $obrazovanie_fiz_lica->dokumentObObrazovaniiSeriya;
            $this->obrDocNomer = $obrazovanie_fiz_lica->dokumentObObrazovaniiNomer;
            $this->obrDocData = DeprecatedDatePicker::fromDatetime($obrazovanie_fiz_lica->dokumentObObrazovaniiDataAsDate);
        }

        return $fiz_lico;
    }

    private function sendSuccessfulEmail($fizLico, $kursId)
    {
        $kurs = Kurs::findOne($kursId);

        $message = Yii::$app->mailer
            ->compose('kurs-slushatelyu/registraciya', compact('fizLico', 'kurs'))
            ->setTo($fizLico->email);

        return $message->send();
    }
}
