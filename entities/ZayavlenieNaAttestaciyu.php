<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 30.07.15
 * Time: 15:16
 */

namespace app\entities;
use app\enums2\TipDolzhnosti;
use app\globals\ApiGlobals;
use yii\db\ActiveRecord;

/**
 * Class ZayavlenieNaAttestaciyu
 * @package app\entities
 * @property int id
 * @property int fiz_lico
 * @property string familiya
 * @property string imya
 * @property string otchestvo
 * @property int ped_stazh
 * @property int stazh_v_dolzhnosti
 * @property int rabota_organizaciya
 * @property int rabota_dolzhnost
 * @property int rabota_stazh_v_dolzhnosti
 * @property int rabota_kopiya_trudovoj_knizhki
 * @property int attestaciya_kategoriya
 * @property int attestaciya_kopiya_attestacionnogo_lista
 * @property string attestaciya_nomer_prikaza
 * @property string na_kategoriyu
 * @property int var_ispytanie_2
 * @property int var_ispytanie_3
 * @property int vremya_provedeniya
 * @property string status
 * @property int vremya_smeny_statusa
 * @property date attestaciya_data_prisvoeniya
 * @property date attestaciya_data_okonchaniya_dejstviya
 * @property string svedeniya_o_sebe
 * @property int svedeniya_o_sebe_fajl
 * @property int var_ispytanie_2_fajl
 * @property int var_ispytanie_3_fajl
 * @property int portfolio
 * @property int prezentatsiya
 * @property string domashnij_telefon
 * @property string rabochij_telefon
 * @property string prilozhenie1
 * @property boolean provesti_zasedanie_bez_prisutstviya
 * @property date rabota_data_naznacheniya
 * @property date rabota_data_naznacheniya_v_uchrezhdenii
 * @property string ld_olimpiady
 * @property string ld_posobiya
 * @property string ld_publikacii
 * @property string ld_prof_konkursy
 * @property string ld_obshestvennaya_aktivnost
 * @property string ld_elektronnye_resursy
 * @property string ld_otkrytoe_meropriyatie
 * @property string ld_nastavnik
 * @property string ld_deti_sns
 * @property date data_rozhdeniya
 * @property int stazh_rukovodyashej_raboty
 * @property int stazh_obshij_trudovoj
 * @property boolean is_fgos
 * @property int informacionnaja_karta
 */

class ZayavlenieNaAttestaciyu extends EntityBase
{
//
//    public function behaviors()
//    {
//        return [
//            'timestamp' => [
//                'class' => TimestampBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_BEFORE_INSERT => 'vremya_smeny_statusa',
//                    ActiveRecord::EVENT_BEFORE_UPDATE => 'vremya_smeny_statusa',
//                ],
//                'value' => function() { return date('U');},
//                ],
//            ];
//    }

    public function getFizLicoRel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'fiz_lico'])->inverseOf('zayavlenieNaAttestaciyuRel');
    }

    public function getVremyaProvedeniyaAttestaciiRel(){
        return $this->hasOne(VremyaProvedeniyaAttestacii::className(),['id'=>'vremya_provedeniya'])->inverseOf('zayavlenieNaAttestaciyuRel');
    }

    public function getOrganizaciyaRel()
    {
        return $this->hasOne(Organizaciya::className(), ['id' => 'rabota_organizaciya'])->inverseOf('zayavlenieNaAttestaciyuRel');
    }

    public function getAdresnyjObjektRel()
    {
        return $this->hasOne(AdresnyjObjekt::className(), ['id' => 'adres_adresnyj_objekt'])->via('organizaciyaRel');
    }

    public function getAttestacionnoeVariativnoeIspytanie2Rel(){
        return $this->hasOne(AttestacionnoeVariativnoeIspytanie_2::className(),['id' => 'var_ispytanie_2'])->inverseOf('zayavlenieNaAttestaciyuRel');
    }

    public function getAttestacionnoeVariativnoeIspytanie3Rel(){
        return $this->hasOne(AttestacionnoeVariativnoeIspytanie_3::className(),['id' => 'var_ispytanie_3'])->inverseOf('zayavlenieNaAttestaciyuRel');
    }

    public function getDolzhnostRel(){
        return $this->hasOne(Dolzhnost::className(),['id'=>'rabota_dolzhnost'])->inverseOf('zayavlenieNaAttestaciyuRel');
    }

    public function getAttestaciyaFajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'attestaciya_kopiya_attestacionnogo_lista'])->inverseOf('zayavlenieNaAttestaciyuKopiyaAttestacionnogoListaRel');
    }

    public function getKopiyaTruidovoiajlRel()
    {
        return $this->hasOne(Fajl::className(),['id'=>'rabota_kopiya_trudovoj_knizhki'])->inverseOf('zayavlenieNaAttestaciyuKopiyaTrudovoiRel')
            ->from(Fajl::tableName() . ' fajl_kopiya_trudovoi');
    }

    public function getObrazovaniyaRel(){
        return $this
            ->hasMany(ObrazovanieDlyaZayavleniyaNaAttestaciyu::className(),['zayavlenie_na_attestaciyu' => 'id'])
            ->andOnCondition(['kurs_tip'=>null])
            ->inverseOf('zayavleniyaNaAttestaciyuObrazovanieRel');
    }

    public function getVarIspytanie2FajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'var_ispytanie_2_fajl'])->inverseOf('zayavlenieNaAttestaciyuVarIsp2FajlRel')
            ->from(Fajl::tableName() . ' var_isp2_fajl');
    }

    public function getVarIspytanie3FajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'var_ispytanie_3_fajl'])->inverseOf('zayavlenieNaAttestaciyuVarIsp3FajlRel')
            ->from(Fajl::tableName() . ' var_isp3_fajl');
    }

    public function getPortfolioFajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'portfolio'])->inverseOf('zayavlenieNaAttestaciyuPortfolioRel')
            ->from(Fajl::tableName() . ' portfolio_fajl');
    }

    public function getPrezentatsiyaFajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'prezentatsiya'])->inverseOf('zayavlenieNaAttestaciyuPrezentatsiyaRel')
            ->from(Fajl::tableName() . ' prezentatsiya_fajl');
    }

    public function getSvedeniyaOSebeFajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'svedeniya_o_sebe_fajl'])->inverseOf('zayavlenieNaAttestaciyuSvedeniyaOSebeRel')
            ->from(Fajl::tableName() . ' svedeniya_o_sebe_fajl');
    }

    public function getKursyRel(){
        return $this->hasMany(ObrazovanieDlyaZayavleniyaNaAttestaciyu::className(),['zayavlenie_na_attestaciyu' => 'id'])
            ->andOnCondition(['is not','obr_kursy.kurs_tip',null])
            ->from(ObrazovanieDlyaZayavleniyaNaAttestaciyu::tableName().' obr_kursy')
            ->inverseOf('zayavleniyaNaAttestaciyuKursyRel');
    }

    public function getRaspredelenieZayavlenijNaAttesctaciyuRel(){
        return $this->hasMany(RaspredelenieZayavlenijNaAttestaciyu::className(),['zayavlenie_na_attestaciyu'=>'id'])->inverseOf('zayavlenieNaAttestaciyuRel');
    }

    public function getOtraslevoeSoglashenieZayavleniyaRel(){
        return $this->hasMany(OtraslevoeSoglashenieZayavleniya::className(), ['zayavlenie_na_attestaciyu' => 'id'])->inverseOf('zayavlenieNaAttestaciyuRel');
    }

    public function getOtsenochnyjListZayvleniyaRel(){
        return $this->hasMany(OtsenochnyjListZayavleniya::className(), ['zayavlenie_na_attestaciyu'=>'id'])
            ->inverseOf('zayavlenieNaAttestaciyuRel');
    }

    public function getInformacionnajaKartaFajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'informacionnaja_karta'])->inverseOf('zayavlenieNaAttestaciyuInformacionnajaKartaRel')
            ->from(Fajl::tableName() . ' ik_fajl');
    }

    public function getPostoyannoeIspytanieOtsenochnogoLista(){
        $otsenochnyjList=OtsenochnyjListZayavleniya::find()->select('postoyannoe_ispytanie')->where(['zayavlenie_na_attestaciyu'=>$this->id])->andWhere(['not',['postoyannoe_ispytanie'=>null]])->one();
        if ($otsenochnyjList){
            return $otsenochnyjList->postoyannoe_ispytanie;
        } else {
            return false;
        }
    }

    public function getIsUchitel(){
        $dolzhnost = $this->find()->with('dolzhnostRel')->where(['id'=>$this->id])->one();
        if ($dolzhnost->dolzhnostRel->tip == TipDolzhnosti::UCHITEL_PREPODAVATEL) {
            return true;
        } else {
            return false;
        }
    }
    
    public function rules()
    {
        return[
          [['fiz_lico','familiya','status','vremya_smeny_statusa'], 'required'],
          [[
              'imya','otchestvo','ped_stazh','stazh_v_dolzhnosti',
              'rabota_organizaciya','rabota_dolzhnost','rabota_stazh_v_dolzhnosti',
              'rabota_kopiya_trudovoj_knizhki','attestaciya_kategoriya','attestaciya_kopiya_attestacionnogo_lista',
              'attestaciya_nomer_prikaza','na_kategoriyu','var_ispytanie_2','var_ispytanie_3',
              'vremya_provedeniya','svedeniya_o_sebe','svedeniya_o_sebe_fajl',
              'var_ispytanie_2_fajl','var_ispytanie_3_fajl','portfolio','prezentatsiya',
              'domashnij_telefon', 'rabochij_telefon', 'prilozhenie1', 'provesti_zasedanie_bez_prisutstviya','data_rozhdeniya',
              'stazh_obshij_trudovoj', 'stazh_rukovodyashej_raboty', 'is_fgos',
          ],'safe'],
        ];
    }

    public function getFio(){
        return $this->familiya.' '.$this->imya.' '.$this->otchestvo;
    }

    public function attributeLabels(){
        return[
          'id'=> 'Номер',
          'fio' => 'ФИО',
          'rabota_stazh_v_dolzhnosti' => 'Стаж в данной должности',
          'prilozhenie1' => 'Приложение №1',
          'ld_olimpiady' => 'Результаты участия обучающихся в предметных олимпиадах, конкурсах',
          'ld_posobiya' => 'Наличие опубликованных собственных методических разработок, методических материалов (программ, учебных и учебно-методических пособий, диагностических материалов, цифровых образовательных ресурсов), прошедших независимую экспертизу, имеющих соответствующий гриф и выходные данные',
          'ld_publikacii' => 'Наличие опубликованных статей, научных публикаций, имеющих соответствующий гриф и выходные данные',
          'ld_prof_konkursy' => 'Результативность участия в профессиональных конкурсах',
          'ld_obshestvennaya_aktivnost' => 'Общественная активность педагога: участие в экспертных комиссиях, предметных комиссиях (ЕГЭ, ГИА), в жюри конкурсов, творческих группах',
          'ld_elektronnye_resursy' => 'Использование электронных образовательных ресурсов (ЭОР) в образовательном процессе',
          'ld_otkrytoe_meropriyatie' => 'Публичное представление собственного педагогического опыта в форме открытого мероприятия',
          'ld_nastavnik' => 'Исполнение функций наставника',
          'ld_deti_sns' => 'Работа с детьми из СНС (социально неблагополучных семей)'
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->prezentatsiya = $this->prezentatsiya ? ApiGlobals::to_trimmed_text($this->prezentatsiya) : null;
            $this->prilozhenie1 = $this->prilozhenie1 ? ApiGlobals::to_trimmed_text($this->prilozhenie1) : null;
            $this->svedeniya_o_sebe = $this->svedeniya_o_sebe ? ApiGlobals::to_trimmed_text($this->svedeniya_o_sebe) : null;
            $this->ld_olimpiady = $this->ld_olimpiady ? ApiGlobals::to_trimmed_text($this->ld_olimpiady) : null;
            $this->ld_posobiya = $this->ld_posobiya ? ApiGlobals::to_trimmed_text($this->ld_posobiya) : null;
            $this->ld_publikacii = $this->ld_publikacii ? ApiGlobals::to_trimmed_text($this->ld_publikacii) : null;
            $this->ld_prof_konkursy = $this->ld_prof_konkursy ? ApiGlobals::to_trimmed_text($this->ld_prof_konkursy) : null;
            $this->ld_obshestvennaya_aktivnost = $this->ld_obshestvennaya_aktivnost ? ApiGlobals::to_trimmed_text($this->ld_obshestvennaya_aktivnost) : null;
            $this->ld_elektronnye_resursy = $this->ld_elektronnye_resursy ? ApiGlobals::to_trimmed_text($this->ld_elektronnye_resursy) : null;
            $this->ld_otkrytoe_meropriyatie = $this->ld_otkrytoe_meropriyatie ? ApiGlobals::to_trimmed_text($this->ld_otkrytoe_meropriyatie) : null;
            $this->ld_nastavnik = $this->ld_nastavnik ? ApiGlobals::to_trimmed_text($this->ld_nastavnik) : null;
            $this->ld_deti_sns = $this->ld_deti_sns ? ApiGlobals::to_trimmed_text($this->ld_deti_sns) : null;
            $this->is_fgos = ($this->is_fgos == 1) ? true : false;
            return true;
        } else {
            return false;
        }
    }
}


