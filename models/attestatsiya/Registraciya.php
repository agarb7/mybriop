<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 03.08.15
 * Time: 12:04
 */

namespace app\models\attestatsiya;


use app\entities\Dolzhnost;
use app\entities\FizLico;
use app\entities\Kvalifikaciya;
use app\entities\ObrazovanieDlyaZayavleniyaNaAttestaciyu;
use app\entities\ObrazovanieFizLica;
use app\entities\Organizaciya;
use app\entities\RabotaFizLica;
use app\entities\ZayavlenieNaAttestaciyu;
use app\enums\KategoriyaPedRabotnika;
use app\enums\StatusZayavleniyaNaAttestaciyu;
use app\globals\ApiGlobals;
use yii\base\Model;

class Registraciya extends Model
{
    public $id;
    public $fizLicoId;
    public $dolzhnost;
    public $attestacionnyListKategoriya;
    public $attestaciyaDataPrisvoeniya;
    public $attestacionnyListPeriodFajl;
    public $varIspytanie2;
    public $varIspytanie3;
    public $vremyaProvedeniya;
    public $pedStazh;
    public $pedStazhVDolzhnosti;
    public $rabotaPedStazhVDolzhnosti;
    public $trudovajya;
    public $visshieObrazovaniya;
    public $kursy;
    public $kategoriya;
    public $status;
    public $svedeniysOSebe;
    public $svedeniysOSebeFajl;
    public $otraslevoeSoglashenie;
    public $domashnijTelefon;
    public $rabochijTelefon;
    public $prilozhenie1;
    public $provestiZasedanieBezPrisutstviya;
    public $rabotaDataNaznacheniya;
    public $rabotaDataNaznacheniyaVUchrezhdenii;
    public $attestaciyaDataOkonchaniyaDejstviya;
    public $ldOlimpiady;
    public $ldPosobiya;
    public $ldPublikacii;
    public $ldProfKonkursy;
    public $ldObshestvennayaAktivnost;
    public $ldElektronnyeResursy;
    public $ldOtkrytoeMeropriyatie;
    public $ldNastavnik;
    public $ldDetiSns;
    public $podtvershdenieNaObrabotku;
    public $dataRozhdeniya;
    public $stazh_obshij_trudovoj;
    public $stazh_rukovodyashej_raboty;
    public $isFgos;
    public $rabotaRajonId;

    public function __construct($zayavlenieId = null){
        parent::__construct();
        if ($zayavlenieId == null) return;
        else{
            $this->id = $zayavlenieId;
            $zayavlenie = ZayavlenieNaAttestaciyu::findOne($zayavlenieId);
            $sql = 'select rabota_fiz_lica.id as rabota_fiz_lica_id, dolzhnost.id as dolzhnost_id,
                       dolzhnost.nazvanie||\', \'||organizaciya.nazvanie as rashirennay_dolzhnost,
                       rabota_fiz_lica.organizaciya,
                       case when adresnyj_objekt.uroven in (\'rajon\',\'gor\')
                        then organizaciya.adres_adresnyj_objekt
                        else coalesce(adresnyj_objekt.roditel_urovnya_rajona,adresnyj_objekt.roditel_urovnya_goroda)
                       end as adres_id,
                       case when adresnyj_objekt.uroven in (\'rajon\',\'gor\')
                        then adresnyj_objekt.oficialnoe_nazvanie
                        else coalesce(rajon.oficialnoe_nazvanie,gorod.oficialnoe_nazvanie)
                       end as adres
                from dolzhnost
                inner join dolzhnost_fiz_lica_na_rabote on dolzhnost.id = dolzhnost_fiz_lica_na_rabote.dolzhnost
                inner join rabota_fiz_lica on rabota_fiz_lica.id = dolzhnost_fiz_lica_na_rabote.rabota_fiz_lica
                inner join organizaciya on rabota_fiz_lica.organizaciya = organizaciya.id
                left join adresnyj_objekt on organizaciya.adres_adresnyj_objekt = adresnyj_objekt.id
                left join adresnyj_objekt as rajon on adresnyj_objekt.roditel_urovnya_rajona = rajon.id
                left join adresnyj_objekt as gorod on adresnyj_objekt.roditel_urovnya_goroda = gorod.id
                where rabota_fiz_lica.fiz_lico = :fiz_lico_id and rabota_fiz_lica.organizaciya= :organizaciya and dolzhnost.id = :dolzhnost_id';
            $result = [];
            $rabota_fiz_lica = \Yii::$app->db->createCommand($sql)
                ->bindValue(':fiz_lico_id',$zayavlenie->fiz_lico)
                ->bindValue(':organizaciya',$zayavlenie->rabota_organizaciya)
                ->bindValue(':dolzhnost_id',$zayavlenie->rabota_dolzhnost)
                ->queryOne();
            $this->fizLicoId = $zayavlenie->fiz_lico;
            $this->dolzhnost = $rabota_fiz_lica['organizaciya'].'_'.$rabota_fiz_lica['dolzhnost_id'].'_'.$rabota_fiz_lica['adres_id'];//$zayavlenie->rabota_dolzhnost;//$rabota_fiz_lica->id;
            $this->attestacionnyListKategoriya = $zayavlenie->attestaciya_kategoriya;
            $this->attestaciyaDataPrisvoeniya = date('d.m.Y',strtotime($zayavlenie->attestaciya_data_prisvoeniya));
            $this->attestacionnyListPeriodFajl = $zayavlenie->attestaciya_kopiya_attestacionnogo_lista;
            $this->varIspytanie2 = $zayavlenie->var_ispytanie_2;
            $this->varIspytanie3 = $zayavlenie->var_ispytanie_3;
            $this->vremyaProvedeniya = $zayavlenie->vremya_provedeniya;
            $this->pedStazh = $zayavlenie->ped_stazh;
            $this->pedStazhVDolzhnosti = $zayavlenie->stazh_v_dolzhnosti;
            $this->rabotaPedStazhVDolzhnosti = $zayavlenie->rabota_stazh_v_dolzhnosti;
            $this->trudovajya = $zayavlenie->rabota_kopiya_trudovoj_knizhki;
            $this->visshieObrazovaniya = [];
            $this->kategoriya = $zayavlenie->na_kategoriyu;
            $this->status = $zayavlenie->status;
            $this->otraslevoeSoglashenie = [];
            $this->domashnijTelefon = $zayavlenie->domashnijTelefon;
            $this->rabochijTelefon = $zayavlenie->rabochijTelefon;
            $this->prilozhenie1 = $zayavlenie->prilozhenie1;
            $this->provestiZasedanieBezPrisutstviya = $zayavlenie->provestiZasedanieBezPrisutstviya;
            $this->rabotaDataNaznacheniya = date('d.m.Y',strtotime($zayavlenie->rabotaDataNaznacheniya));
            $this->rabotaDataNaznacheniyaVUchrezhdenii = date('d.m.Y',strtotime($zayavlenie->rabotaDataNaznacheniya_vUchrezhdenii));
            $this->attestaciyaDataOkonchaniyaDejstviya = date('d.m.Y', strtotime($zayavlenie->attestaciyaDataOkonchaniyaDejstviya));
            $this->ldOlimpiady = $zayavlenie->ld_olimpiady;
            $this->ldPosobiya = $zayavlenie->ld_posobiya;
            $this->ldPublikacii = $zayavlenie->ld_publikacii;
            $this->ldProfKonkursy = $zayavlenie->ld_prof_konkursy;
            $this->ldObshestvennayaAktivnost = $zayavlenie->ld_obshestvennaya_aktivnost;
            $this->ldElektronnyeResursy = $zayavlenie->ld_elektronnye_resursy;
            $this->ldOtkrytoeMeropriyatie = $zayavlenie->ld_otkrytoe_meropriyatie;
            $this->ldNastavnik = $zayavlenie->ld_nastavnik;
            $this->ldDetiSns = $zayavlenie->ld_deti_sns;
            $this->dataRozhdeniya = date('d.m.Y', strtotime($zayavlenie->data_rozhdeniya));;
            $this->podtvershdenieNaObrabotku =true;
            $this->stazh_rukovodyashej_raboty = $zayavlenie->stazh_rukovodyashej_raboty;
            $this->stazh_obshij_trudovoj = $zayavlenie->stazh_obshij_trudovoj;
            $this->isFgos = $zayavlenie->is_fgos;
        }
    }

    public function attributeLabels(){
        return[
            'dolzhnost' => 'Должность',
            'attestacionnyListKategoriya' => 'Категория',
            'attestaciyaDataPrisvoeniya' => 'Дата присвоения',
            'attestacionnyListPeriodFajl' => 'Копия',
            'varIspytanie2' => 'Второе вариативное испытание',
            'varIspytanie3' => 'Третье вариативное испытание',
            'vremyaProvedeniya' => 'Время проведения аттестации',
            'pedStazh' => 'Общий педагогический стаж',
            'pedStazhVDolzhnosti' => 'Стаж в занимаемой должности',
            'rabotaPedStazhVDolzhnosti' => 'Cтаж в данном образовательном учреждении по занимаемой должности',
            'trudovajya' => 'Копия трудовой книжки',
            'kategoriya' => 'Категория, на которую будет производиться аттестация',
            'svedeniysOSebe' => 'Сведения о себе',
            'svedeniysOSebeFajl' => 'Сведения о себе (файл)',
            'domashnijTelefon' => 'Личный телефон',
            'rabochijTelefon' => 'Рабочий телефон',
            'prilozhenie1' => 'Приложение №1 (Основание для аттестации)',
            'provestiZasedanieBezPrisutstviya' => 'Провести заседание аттестационной комиссии без моего присутствия',
            'attestaciyaDataOkonchaniyaDejstviya' => 'Дата окончания действия',
            'rabotaDataNaznacheniya' => 'Впервые',
            'rabotaDataNaznacheniyaVUchrezhdenii' => 'В данном учреждении',
            'ldOlimpiady' => 'Результаты участия обучающихся в предметных олимпиадах, конкурсах',
            'ldPosobiya' => 'Наличие опубликованных собственных методических разработок, методических материалов (программ, учебных и учебно-методических пособий, диагностических материалов, цифровых образовательных ресурсов), прошедших независимую экспертизу, имеющих соответствующий гриф и выходные данные',
            'ldPublikacii' => 'Наличие опубликованных статей, научных публикаций, имеющих соответствующий гриф и выходные данные',
            'ldProfKonkursy' => 'Результативность участия в профессиональных конкурсах',
            'ldObshestvennayaAktivnost' => 'Общественная активность педагога: участие в экспертных комиссиях, предметных комиссиях (ЕГЭ, ГИА), в жюри конкурсов, творческих группах',
            'ldElektronnyeResursy' => 'Использование электронных образовательных ресурсов (ЭОР) в образовательном процессе',
            'ldOtkrytoeMeropriyatie' => 'Публичное представление собственного педагогического опыта в форме открытого мероприятия',
            'ldNastavnik' => 'Исполнение функций наставника',
            'ldDetiSns' => 'Работа с детьми из СНС (социально неблагополучных семей)',
            'podtvershdenieNaObrabotku' => 'Согласие на обработку персональных данных',
            'dataRozhdeniya' => 'Дата рождения',
            'stazh_rukovodyashej_raboty' => 'Руководящей работы',
            'stazh_obshij_trudovoj' => 'Общий трудовой',
            'isFgos' => 'Провести аттестацию согласно критериям ФГОС',
        ];
    }

    public function is11NumbersOnly($attribute)
    {
        if (!preg_match('/^[0-9]{11}$/', $this->$attribute)) {
            $this->addError($attribute, 'телефон должен состять из 11 цифр');
        }
    }

    public function rules(){
        return [
            [['dolzhnost','vremyaProvedeniya','attestacionnyListKategoriya',
              'pedStazh','rabotaPedStazhVDolzhnosti',
              'trudovajya','kategoriya',
              'provestiZasedanieBezPrisutstviya','rabotaDataNaznacheniya',
              'rabotaDataNaznacheniyaVUchrezhdenii', 'domashnijTelefon', 'dataRozhdeniya', 'rabochijTelefon', //'rabotaRajonId'
            ],'required'],
            [['stazh_obshij_trudovoj', 'stazh_rukovodyashej_raboty', 'pedStazhVDolzhnosti'], 'safe'],
            [['domashnijTelefon','rabochijTelefon'], 'integer', 'message'=>'телефон должен состоять из 11 цифр'],
            [['domashnijTelefon','rabochijTelefon'], 'is11NumbersOnly'],
            [
                ['attestaciyaDataPrisvoeniya','attestacionnyListPeriodFajl','attestaciyaDataOkonchaniyaDejstviya'],'required',
                'when'=>function($model){
                    return $model->attestacionnyListKategoriya != KategoriyaPedRabotnika::BEZ_KATEGORII;
                },
                'whenClient' => "function (attribute, value) {
                                        return $('#attestacionnyListKategoriya').val() != '".KategoriyaPedRabotnika::BEZ_KATEGORII."';
                                    }"
            ],
            [['dolzhnost'],'compare','compareValue'=>-1,'type'=>'number','operator'=> '!=','message'=>'Выберите «Должность» из списка'],
            [['fizLicoId','visshieObrazovaniya','kursy','status','id','varIspytanie2',
                'svedeniysOSebe','svedeniysOSebeFajl','otraslevoeSoglashenie',
            'ldOlimpiady', 'ldPosobiya', 'ldPublikacii', 'ldProfKonkursy',
            'ldObshestvennayaAktivnost', 'ldElektronnyeResursy','ldOtkrytoeMeropriyatie',
            'ldNastavnik', 'ldDetiSns','isFgos'],'safe'],
//            [['varIspytanie2'],'required','when'=>function($model){
//                    return $model->kategoriya == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA;
//                },
//                'whenClient' => "function (attribute, value) {
//                                        return $('#kategoriya').val() == '".KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA."';
//                                    }"
//            ],
            [['prilozhenie1'],'required', 'when' => function($model){
                return $model->kategoriya == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA;
            },
            'whenClient' => "function (attribute, value) {
                                    return $('#kategoriya').val() == '".KategoriyaPedRabotnika::PERVAYA_KATEGORIYA."';
                                }"
            ],
            [['varIspytanie3'],'required','when'=>function($model){
                    return ($model->kategoriya == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA and
                        count($model->otraslevoeSoglashenie) == 0);
                },
                'whenClient' => "function (attribute, value) {
                                        return $('#kategoriya').val() == '".KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA."';
                                    }"
            ]
        ];
    }

    public static function getDolzhnostiFizLicaToSelect($fizLicoId, $onlyDolzhnost = false){
        $sql = 'select rabota_fiz_lica.id as rabota_fiz_lica_id, dolzhnost.id as dolzhnost_id,
                       dolzhnost.nazvanie||\', \'||organizaciya.nazvanie as rashirennay_dolzhnost,
                       rabota_fiz_lica.organizaciya,
                       case when adresnyj_objekt.uroven in (\'rajon\',\'gor\')
                        then organizaciya.adres_adresnyj_objekt
                        else coalesce(adresnyj_objekt.roditel_urovnya_rajona,adresnyj_objekt.roditel_urovnya_goroda)
                       end as adres_id,
                       case when adresnyj_objekt.uroven in (\'rajon\',\'gor\')
                        then adresnyj_objekt.oficialnoe_nazvanie
                        else coalesce(rajon.oficialnoe_nazvanie,gorod.oficialnoe_nazvanie)
                       end as adres
                from dolzhnost
                inner join dolzhnost_fiz_lica_na_rabote on dolzhnost.id = dolzhnost_fiz_lica_na_rabote.dolzhnost
                inner join rabota_fiz_lica on rabota_fiz_lica.id = dolzhnost_fiz_lica_na_rabote.rabota_fiz_lica
                inner join organizaciya on rabota_fiz_lica.organizaciya = organizaciya.id
                left join adresnyj_objekt on organizaciya.adres_adresnyj_objekt = adresnyj_objekt.id
                left join adresnyj_objekt as rajon on adresnyj_objekt.roditel_urovnya_rajona = rajon.id
                left join adresnyj_objekt as gorod on adresnyj_objekt.roditel_urovnya_goroda = gorod.id
                where rabota_fiz_lica.fiz_lico = :fiz_lico_id';
        $result = [];
        $queryResult = \Yii::$app->db->createCommand($sql)
                                     ->bindValue(':fiz_lico_id',$fizLicoId)->queryAll();
        foreach ($queryResult as $k=>$v) {
            if (!$onlyDolzhnost) {
                $result[$v['organizaciya'].'_'.$v['dolzhnost_id'].'_'.$v['adres_id']] = $v['rashirennay_dolzhnost'].', '.$v['adres'];
            }
            else{
                $result[$v['rabota_fiz_lica_id']] = $v['dolzhnost_id'];
            }
        }

        return $result;
    }

    private function parseAttestaciyaDate(){
        $result = ['data_prisvoeniya'=>'','data_okonchaniya_dejstviya'=>''];
        if ($this->attestacionnyListPeriodDejstviya){
            $dates = explode(' - ', $this->attestacionnyListPeriodDejstviya);
            if ($dates){
                $result['data_prisvoeniya'] = trim($dates[0]);
                $result['data_okonchaniya_dejstviya'] = trim($dates[1]);
            }
        }
        return $result;
    }

    public function save(){
        $fizLicoFio = FizLico::getFioById($this->fizLicoId);
        $rabota = explode('_', $this->dolzhnost);//RabotaFizLica::find()->joinWith('dolzhnostiFizLicaNaRaboteRel')->where(['dolzhnost_fiz_lica_na_rabote.dolzhnost'=>$this->dolzhnost])->andWhere(['rabota_fiz_lica.fiz_lico'=>$this->fizLicoId])->one();
        //var_dump($rabota);die();
        $zayavlenie = ZayavlenieNaAttestaciyu::findOne($this->id ? $this->id : 0);
        //$attestaciyaDates = $this->parseAttestaciyaDate();
        if (!$zayavlenie) $zayavlenie = new ZayavlenieNaAttestaciyu();
        $zayavlenie->fiz_lico =  $this->fizLicoId;
        $zayavlenie->familiya =  $fizLicoFio['familiya'];
        $zayavlenie->imya =  $fizLicoFio['imya'];
        $zayavlenie->otchestvo =  $fizLicoFio['otchestvo'];
        $zayavlenie->ped_stazh =  $this->pedStazh;
        $zayavlenie->is_fgos = $this->isFgos;
        $zayavlenie->rabota_organizaciya =  $rabota[0];//->organizaciya;
        $zayavlenie->rabota_dolzhnost =  $rabota[1];//dolzhnostiFizLicaNaRaboteRel[0]->dolzhnost;
        $zayavlenie->rabota_stazh_v_dolzhnosti =  $this->rabotaPedStazhVDolzhnosti;
        $zayavlenie->rabota_kopiya_trudovoj_knizhki =  $this->trudovajya;
        $zayavlenie->attestaciya_kategoriya =  $this->attestacionnyListKategoriya;
        $zayavlenie->attestaciya_kopiya_attestacionnogo_lista =  $this->attestacionnyListPeriodFajl;
        $zayavlenie->attestaciya_data_prisvoeniya = date('Y-m-d',strtotime($this->attestaciyaDataPrisvoeniya));
        $zayavlenie->attestaciya_data_okonchaniya_dejstviya = date('Y-m-d',strtotime($this->attestaciyaDataOkonchaniyaDejstviya));
        $zayavlenie->na_kategoriyu =  $this->kategoriya;
        $zayavlenie->rabota_data_naznacheniya = date('Y-m-d',strtotime($this->rabotaDataNaznacheniya));
        $zayavlenie->rabota_data_naznacheniya_v_uchrezhdenii = date('Y-m-d',strtotime($this->rabotaDataNaznacheniyaVUchrezhdenii));
        $zayavlenie->data_rozhdeniya = date('Y-m-d', strtotime($this->dataRozhdeniya));
        if ($zayavlenie->rabota_dolzhnost == 47) {
            $zayavlenie->stazh_obshij_trudovoj = $this->stazh_obshij_trudovoj;
            $zayavlenie->stazh_rukovodyashej_raboty = $this->stazh_rukovodyashej_raboty;
            $zayavlenie->stazh_v_dolzhnosti =  null;
        }
        else{
            $zayavlenie->stazh_obshij_trudovoj = null;
            $zayavlenie->stazh_rukovodyashej_raboty = null;
            $zayavlenie->stazh_v_dolzhnosti =  $this->pedStazhVDolzhnosti;
        }

        if ($this->kategoriya == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA) {
            $zayavlenie->svedeniya_o_sebe = $this->svedeniysOSebe ? $this->svedeniysOSebe : null;
            $zayavlenie->svedeniya_o_sebe_fajl = $this->svedeniysOSebeFajl;
        }
        else{
            $zayavlenie->svedeniya_o_sebe = null;
            $zayavlenie->svedeniya_o_sebe_fajl = null;
        }
        if ($this->kategoriya == KategoriyaPedRabotnika::BEZ_KATEGORII){
            $zayavlenie->var_ispytanie_2 = null;
            $zayavlenie->var_ispytanie_3 = null;
        }
        else {
            $zayavlenie->var_ispytanie_2 = $this->kategoriya == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA ? $this->varIspytanie2 : null;
            $zayavlenie->var_ispytanie_3 = $this->kategoriya == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA ? $this->varIspytanie3 : null;
            if (count($this->otraslevoeSoglashenie) > 0){
                $zayavlenie->var_ispytanie_3 = null;
            }
            if ($zayavlenie->rabota_dolzhnost == 47){
                $zayavlenie->var_ispytanie_3 = null;
            }
        }
        $zayavlenie->vremya_provedeniya = $this->vremyaProvedeniya;
        $this->status = $this->status ? $this->status : StatusZayavleniyaNaAttestaciyu::REDAKTIRUETSYA_PED_RABOTNIKOM;
        $zayavlenie->status =  $this->status ? $this->status : StatusZayavleniyaNaAttestaciyu::REDAKTIRUETSYA_PED_RABOTNIKOM;
        $zayavlenie->vremya_smeny_statusa =  date("Y-m-d H:i:s");
        $zayavlenie->domashnijTelefon = substr($this->domashnijTelefon,1);
        $zayavlenie->rabochijTelefon = substr($this->rabochijTelefon,1);
        $zayavlenie->provestiZasedanieBezPrisutstviya = $this->provestiZasedanieBezPrisutstviya;
        $zayavlenie->prilozhenie1 = $this->prilozhenie1;
        $zayavlenie->ld_olimpiady = $this->ldOlimpiady ? $this->ldOlimpiady : null;
        $zayavlenie->ld_posobiya = $this->ldPosobiya ? $this->ldPosobiya : null;
        $zayavlenie->ld_publikacii = $this->ldPublikacii ? $this->ldPublikacii : null;
        $zayavlenie->ld_prof_konkursy = $this->ldProfKonkursy ? $this->ldProfKonkursy : null;
        $zayavlenie->ld_obshestvennaya_aktivnost = $this->ldObshestvennayaAktivnost ? $this->ldObshestvennayaAktivnost : null;
        $zayavlenie->ld_elektronnye_resursy = $this->ldElektronnyeResursy ? $this->ldElektronnyeResursy : null;
        $zayavlenie->ld_otkrytoe_meropriyatie = $this->ldOtkrytoeMeropriyatie ? $this->ldOtkrytoeMeropriyatie : null;
        $zayavlenie->ld_nastavnik = $this->ldNastavnik ? $this->ldNastavnik : null;
        $zayavlenie->ld_deti_sns = $this->ldDetiSns ? $this->ldDetiSns : null;
        if (!$zayavlenie->validate()) {
            return false;
        }
        //создаем массив с высшими образованиями
        $Obrazovaniya = [];
        if ($this->visshieObrazovaniya){
            foreach ($this->visshieObrazovaniya as $k=>$v) {
                $object = ['tip'=>'vo','index'=>$k];
                if ($v->udalit) $object['udalit'] = 1;
                else $object['udalit'] = 0;
                $dataVidachi = date('Y-m-d',strtotime($v->dataVidachi));
                $obrazovanieFizLica = ObrazovanieFizLica::findOne($v->obrazovanieFizLicaId ? $v->obrazovanieFizLicaId : 0);
                if (!$obrazovanieFizLica) $obrazovanieFizLica = new ObrazovanieFizLica();
                $obrazovanieFizLica->fiz_lico = $this->fizLicoId;
                $obrazovanieFizLica->dokument_ob_obrazovanii_tip = $v->tipDokumenta;
                $obrazovanieFizLica->dokument_ob_obrazovanii_seriya = $v->seriya;
                $obrazovanieFizLica->dokument_ob_obrazovanii_nomer = $v->nomer;
                $obrazovanieFizLica->dokument_ob_obrazovanii_data = $dataVidachi;
                $obrazovanieFizLica->kvalifikaciya = $v->kvalifikaciyaId;
                $obrazovanieFizLica->organizaciya = $v->organizaciyaId;
                if (!$v->kvalifikaciyaId and $v->kvalifikaciyaNazvanie)
                    $object['novayaKvalifikaciya'] = ApiGlobals::to_trimmed_text($v->kvalifikaciyaNazvanie);
                else
                    $object['novayaKvalifikaciya'] = '';
                if (!$v->organizaciyaId and $v->organizaciyaNazvanie)
                    $object['novayaOrganizaciya'] = ApiGlobals::to_trimmed_text($v->organizaciyaNazvanie);
                else
                    $object['novayaOrganizaciya'] = '';
                $obrazovanieFizLica->dokument_ob_obrazovanii_kopiya = $v->documentKopiya;
                if (!$obrazovanieFizLica->validate()) {
                    return false;
                }
                $object['obrazovanieFizLica'] = $obrazovanieFizLica;
                $obrazovanieDlyaZayavleniya = ObrazovanieDlyaZayavleniyaNaAttestaciyu::findOne($v->obrazovanieDlyaZayavleniyaId ? $v->obrazovanieDlyaZayavleniyaId : 0);
                if (!$obrazovanieDlyaZayavleniya) $obrazovanieDlyaZayavleniya = new ObrazovanieDlyaZayavleniyaNaAttestaciyu();
                $obrazovanieDlyaZayavleniya->zayavlenie_na_attestaciyu = $this->id;
                $obrazovanieDlyaZayavleniya->obrazovanie_istochnik = $v->obrazovanieFizLicaId;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_tip = $v->tipDokumenta;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_seriya = $v->seriya;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_nomer = $v->nomer;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_data = $dataVidachi;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_kopiya = $v->documentKopiya;
                $obrazovanieDlyaZayavleniya->kvalifikaciya = $v->kvalifikaciyaId;
                $obrazovanieDlyaZayavleniya->organizaciya = $v->organizaciyaId;
                $object['obrazovanieDlyaZayavlaniya'] = $obrazovanieDlyaZayavleniya;
                $Obrazovaniya[] = $object;
            }
        }

        if ($this->kursy){
            foreach ($this->kursy as $k=>$v) {
                $object = ['tip'=>'kurs','index'=>$k];
                if ($v->udalit) $object['udalit'] = 1;
                else $object['udalit'] = 0;
                $dataVidachi = date('Y-m-d',strtotime($v->dataVidachi));
                $obrazovanieFizLica = ObrazovanieFizLica::findOne($v->obrazovanieFizLicaId ? $v->obrazovanieFizLicaId : 0);
                if (!$obrazovanieFizLica) $obrazovanieFizLica = new ObrazovanieFizLica();
                $obrazovanieFizLica->fiz_lico = $this->fizLicoId;
                $obrazovanieFizLica->dokument_ob_obrazovanii_tip = $v->tipDokumenta;
                $obrazovanieFizLica->dokument_ob_obrazovanii_data = $dataVidachi;
                $obrazovanieFizLica->organizaciya = $v->organizaciyaId;
                $obrazovanieFizLica->kurs_nazvanie = preg_replace('/ {2,}/',' ',trim($v->kursNazvanie));
                $obrazovanieFizLica->kurs_chasy = $v->kursChasy;
                $obrazovanieFizLica->kurs_tip = $v->kursTip;
                $obrazovanieFizLica->dokument_ob_obrazovanii_kopiya = $v->documentKopiya;
                $obrazovanieFizLica->dokument_ob_obrazovanii_seriya = null;
                $obrazovanieFizLica->dokument_ob_obrazovanii_nomer = null;
                if (!$v->organizaciyaId and $v->organizaciyaNazvanie)
                    $object['novayaOrganizaciya'] = ApiGlobals::to_trimmed_text($v->organizaciyaNazvanie);
                else
                    $object['novayaOrganizaciya'] = '';
                $object['novayaKvalifikaciya'] = '';
                if (!$obrazovanieFizLica->validate()) {
                    return false;
                }
                $object['obrazovanieFizLica'] = $obrazovanieFizLica;
                $obrazovanieDlyaZayavleniya = ObrazovanieDlyaZayavleniyaNaAttestaciyu::findOne($v->obrazovanieDlyaZayavleniyaId ? $v->obrazovanieDlyaZayavleniyaId : 0);
                if (!$obrazovanieDlyaZayavleniya) $obrazovanieDlyaZayavleniya = new ObrazovanieDlyaZayavleniyaNaAttestaciyu();
                $obrazovanieDlyaZayavleniya->zayavlenie_na_attestaciyu = $this->id;
                $obrazovanieDlyaZayavleniya->obrazovanie_istochnik = $v->obrazovanieFizLicaId;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_tip = $v->tipDokumenta;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_data = $dataVidachi;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_kopiya = $v->documentKopiya;
                $obrazovanieDlyaZayavleniya->organizaciya = $v->organizaciyaId;
                $obrazovanieDlyaZayavleniya->kurs_nazvanie = preg_replace('/ {2,}/',' ',trim($v->kursNazvanie));
                $obrazovanieDlyaZayavleniya->kurs_chasy = $v->kursChasy;
                $obrazovanieDlyaZayavleniya->kurs_tip = $v->kursTip;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_nomer = null;
                $obrazovanieDlyaZayavleniya->dokument_ob_obrazovanii_seriya = null;
                $object['obrazovanieDlyaZayavlaniya'] = $obrazovanieDlyaZayavleniya;
                $Obrazovaniya[] = $object;
            }
        }

        $saveTransaction = RabotaFizLica::getDb()->transaction(
            function () use (
                $zayavlenie, $Obrazovaniya
            ) {
                //var_dump($zayavlenie->save(false));
                if(!$zayavlenie->save(false)) {
                    var_dump('zayavl_error');
                    return false;
                }
                $this->id = $zayavlenie->id;
                foreach($Obrazovaniya as $k=>$v){
                    if ($v['udalit']){
                        if (!$v['obrazovanieDlyaZayavlaniya']->delete()) return false;
                        if ($v['tip'] == 'kurs')
                            if (!$v['obrazovanieFizLica']->delete()) return false;
                        if ($v['tip']=='vo')
                            unset($this->visshieObrazovaniya[$v['index']]);
                        else
                            unset($this->kursy[$v['index']]);
                    }
                    else {
                        if ($v['novayaKvalifikaciya']) {
                            $kvalifikaciya = new Kvalifikaciya([
                                'nazvanie' => ApiGlobals::to_trimmed_text($v['novayaKvalifikaciya']),
                                'obschij' => false
                            ]);
                            if (!$kvalifikaciya->save(false)) {
                                var_dump('kvalifik_error');
                                return false;
                            }
                            $v['obrazovanieFizLica']->kvalifikaciya = $kvalifikaciya->id;
                            $v['obrazovanieDlyaZayavlaniya']->kvalifikaciya = $kvalifikaciya->id;
                        }
                        if ($v['novayaOrganizaciya']) {
                            $organizaciya = new Organizaciya([
                                'nazvanie' => ApiGlobals::to_trimmed_text($v['novayaOrganizaciya']),
                                'obschij' => false,
                                'etapy_obrazovaniya' => '{' . \app\enums\EtapObrazovaniya::VYSSHEE_PROFESSIONALNOE_OBRAZOVANIE . '}'
                            ]);
                            if (!$organizaciya->save(false)) {
                                var_dump('orghanizac_error');
                                return false;
                            }
                            $v['obrazovanieFizLica']->organizaciya = $organizaciya->id;
                            $v['obrazovanieDlyaZayavlaniya']->organizaciya = $organizaciya->id;
                        }
                        if (!$v['obrazovanieFizLica']->validate()) return false;
                        if (!$v['obrazovanieFizLica']->save(false)) {
                            var_dump('obrfl_error');
                            return false;
                        }
                        if ($v['tip'] == 'vo')
                            $this->visshieObrazovaniya[$v['index']]->obrazovanieFizLicaId = $v['obrazovanieFizLica']->id;
                        else
                            $this->kursy[$v['index']]->obrazovanieFizLicaId = $v['obrazovanieFizLica']->id;
                        $v['obrazovanieDlyaZayavlaniya']->obrazovanie_istochnik = $v['obrazovanieFizLica']->id;
                        $v['obrazovanieDlyaZayavlaniya']->zayavlenie_na_attestaciyu = $zayavlenie->id;
                        if (!$v['obrazovanieDlyaZayavlaniya']->validate()) return false;
                        if (!$v['obrazovanieDlyaZayavlaniya']->save(false)) {
                            var_dump('obrzZayavl_error');
                            return false;
                        }
                        if ($v['tip'] == 'vo')
                            $this->visshieObrazovaniya[$v['index']]->obrazovanieDlyaZayavleniyaId = $v['obrazovanieDlyaZayavlaniya']->id;
                        else
                            $this->kursy[$v['index']]->obrazovanieDlyaZayavleniyaId = $v['obrazovanieDlyaZayavlaniya']->id;
                    }
                }
                foreach ($this->otraslevoeSoglashenie as $key => $item) {
                    /**
                     * @var OtraslevoeSoglashenie $item
                     */
                    $item->zayavlenieNaAttestaciyu = $this->id;
                    if ($savedItem = $item->save()){
                        if ($item->udalit or $zayavlenie->rabota_dolzhnost == 47){
                            unset($this->otraslevoeSoglashenie[$key]);
                        }
                        else {
                            $item->id = $savedItem->id;
                        }
                    }
                    else{
                        var_dump('os_error');
                        return false;
                    }
                }
                return true;
            }
        );
        if (!$saveTransaction) return false;
        return true;
    }
}