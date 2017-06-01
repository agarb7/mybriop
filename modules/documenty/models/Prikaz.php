<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 03.03.2017
 * Time: 10:13
 */

namespace app\modules\documenty\models;

use app\entities\Polzovatel;
use app\enums\Rol;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use Yii;

class Prikaz extends Model
{
    public $id;
    public $nomerRegistracii;
    public $dataRegistracii;
    public $statusPodpisan;
    public $shablonId;
    public $avtorId; // polzovatel id
    public $dataSozdanija;

    public $atributy; // массив атрибутов приказа
    public $slushateli; // массив слушателей курса
    public $komissija; // массив состава комиссии итоговой аттестации

    public function __construct($prikazId = null)
    {
        parent::__construct();
        if ($prikazId == null) return;
        else{
            $this->id = $prikazId;
            $p = DokPrikaz::findOne(['id' => $prikazId]);
            $this->nomerRegistracii = $p['nomer_registracii'];
            $this->dataRegistracii = $p['data_registracii'];
            $this->shablonId = $p['shablon_id'];
            $this->avtorId = $p['avtor_id'];
            $this->dataSozdanija = $p['data_sozdanija'];
            $this->statusPodpisan = $p['status_podpisan'];

            $pa = DokPrikazAtribut::find()->where(['prikaz_id' => $prikazId])->asArray()->all();
            $this->atributy = [];
            foreach ($pa as $v) {
                if (isset($v['znachenie'])) $this->atributy[$v['atribut_id']] = $v['znachenie'];
                else $this->atributy[$v['atribut_id']] = $v['id_znachenija'];
            }

            $pt = DokPrikazTablica::find()->where(['prikaz_id' => $prikazId])->orderBy('id')->asArray()->all();
            $this->slushateli = [];
            $this->komissija = [];
            foreach ($pt as $v){
                if (isset($v['kurs_fiz_lica_id'])) $this->slushateli[] = $v['kurs_fiz_lica_id'];
                elseif(isset($v['fiz_lico_id'])) $this->komissija[] = $v['fiz_lico_id'];
            }
        }
    }

    public function attributeLabels()
    {
        return[
            'nomerRegistracii' => 'Номер',
            'dataRegistracii' => 'Дата регистрации',
            'statusPodpisan' => 'Подписан',
            'dataPodpisanija' => 'Дата подписания',
            'dataSozdanija' => 'Дата создания',
            'atributy' => 'Реквизиты',
        ];
    }
    
    public function rules()
    {
        return [
            [['shablonId','avtorId','dataSozdanija',],'required'],
            [['id','shablonId','avtorId'],'integer'],
            ['slushateli', 'required', 'message' => 'список слушателей пуст;'],
            ['atributy', 'each', 'rule' => ['required', 'message' => 'введите все значения реквизитов приказа;']],
            ['komissija', 'each', 'rule' => ['required', 'message' => 'выберите трех членов комиссии;']],
        ];
    }
    
    public function getShablonFileName($shablonId)
    {
        $shablon=DokPrikazShablon::find()->where(['id'=>$shablonId])->one();
        return $shablon->shablon.'.php';
    }
    
    public function getYearsPlanProspekt()
    {
        return ArrayHelper::map((new \yii\db\Query())
            ->select(['EXTRACT(YEAR FROM plan_prospekt_god) as year'])
            ->from('kurs')
            ->distinct()
            ->orderBy('year')
            ->all(),'year','year');
    }
    
    public function getSlushateliKursa($kursId)
    {
        $query = (new \yii\db\Query())
            ->select(['kurs_fiz_lica.id AS kurs_fiz_lica_id'
                ,'fiz_lico.familiya AS familiya'
                ,'fiz_lico.imya AS imya'
                ,'fiz_lico.otchestvo AS otchestvo'
                ,'organizaciya.nazvanie AS organizaciya'
                ,'adresnyj_objekt.oficialnoe_nazvanie AS rajon'])
            ->from('kurs_fiz_lica')
            ->innerJoin('fiz_lico', 'fiz_lico.id = kurs_fiz_lica.fiz_lico')
            ->innerJoin('dolzhnost_fiz_lica_na_rabote', 'dolzhnost_fiz_lica_na_rabote.id = kurs_fiz_lica.dolzhnost_fiz_lica_na_rabote')
            ->innerJoin('rabota_fiz_lica', 'rabota_fiz_lica.id = dolzhnost_fiz_lica_na_rabote.rabota_fiz_lica')
            ->innerJoin('organizaciya', 'organizaciya.id = rabota_fiz_lica.organizaciya')
            ->innerJoin('adresnyj_objekt', 'adresnyj_objekt.id = organizaciya.adres_adresnyj_objekt')
            ->where(['kurs_fiz_lica.kurs' => $kursId])
            ->orderBy('familiya')
            ->all();
        $i=0;
        $data = [];
        foreach (ArrayHelper::index($query,'kurs_fiz_lica_id') as $key => $value){
            $data[$i] = array( 'id' => $key
            ,'fio' => $value['familiya'].' '.$value['imya'].' '.$value['otchestvo']
            ,'organizaciya' => $value['organizaciya']
            ,'rajon' => $value['rajon'],
            );
            $i++;
        };
        return  $data;
    }
    
    public function getSlushateliPrikaza($pid)
    {
        $query = (new \yii\db\Query())
            ->select(['kurs_fiz_lica.id AS kurs_fiz_lica_id'
                ,'fiz_lico.familiya AS familiya'
                ,'fiz_lico.imya AS imya'
                ,'fiz_lico.otchestvo AS otchestvo'
                ,'organizaciya.nazvanie AS organizaciya'
                ,'adresnyj_objekt.oficialnoe_nazvanie AS rajon'])
            ->from(['dpk' => 'dok_prikaz_tablica'])
            ->innerJoin('kurs_fiz_lica', 'kurs_fiz_lica.id = dpk.kurs_fiz_lica_id')
            ->innerJoin('fiz_lico', 'fiz_lico.id = kurs_fiz_lica.fiz_lico')
            ->innerJoin('dolzhnost_fiz_lica_na_rabote', 'dolzhnost_fiz_lica_na_rabote.id = kurs_fiz_lica.dolzhnost_fiz_lica_na_rabote')
            ->innerJoin('rabota_fiz_lica', 'rabota_fiz_lica.id = dolzhnost_fiz_lica_na_rabote.rabota_fiz_lica')
            ->innerJoin('organizaciya', 'organizaciya.id = rabota_fiz_lica.organizaciya')
            ->innerJoin('adresnyj_objekt', 'adresnyj_objekt.id = organizaciya.adres_adresnyj_objekt')
            ->where(['dpk.prikaz_id' => $pid])
            ->orderBy('familiya')
            ->all();
        $i=0; $data = [];
        //var_dump($query);die();
        foreach (ArrayHelper::index($query,'kurs_fiz_lica_id') as $key => $value){
            $data[$i] = array( 'id' => $key
            ,'fio' => $value['familiya'].' '.$value['imya'].' '.$value['otchestvo']
            ,'organizaciya' => $value['organizaciya']
            ,'rajon' => $value['rajon'],
            );
            $i++;
        };
        //var_dump($data);die();
        return  $data;
    }

    public function getSotrudniki()
    {
        $sql='SELECT DISTINCT fl.id, fl.familiya||\' \'||fl.imya||\' \'||fl.otchestvo as fio
              FROM fiz_lico as fl
                INNER JOIN rabota_fiz_lica as rfl on fl.id=rfl.fiz_lico
                INNER JOIN dolzhnost_fiz_lica_na_rabote as dnr on rfl.id = dnr.rabota_fiz_lica
                INNER JOIN dolzhnost as d on dnr.dolzhnost = d.id
                where d.tip = \'profprep\' AND rfl.organizaciya = \'1\'
              ORDER BY fio';
        $sotrudniki = [];
        if ($res = Yii::$app->db->createCommand($sql)->queryAll()){
            foreach ($res as $v) $sotrudniki[$v['id']] = $v['fio'];
        }
        return $sotrudniki;
    }

    public function getKomissija($prikazId)
    {
        //$query = DokPrikazTablica::find()->with('chlenKomissii')->where(['prikaz_id'=>$prikazId])->andWhere(['not', ['fiz_lico_id' => null]])->asArray()->all();
        $sql='select pt.fiz_lico_id, fl.familiya||\' \'||fl.imya||\' \'||fl.otchestvo as fio
            from dok_prikaz_tablica as pt
            inner join fiz_lico as fl on pt.fiz_lico_id = fl.id
            where pt.prikaz_id ='.$prikazId.' order by pt.id';

        $komissija = [];
        $res = Yii::$app->db->createCommand($sql)->queryAll();
        //var_dump($res);
        if ($res){
            foreach ($res as $v) $komissija[$v['fiz_lico_id']] = $v['fio'];
        }
        return $komissija;
    }

    public function getAvtor($avtorId)
    {
        $fl = DokPrikaz::find()->with('fizLicoRel')->where(['avtor_id' => $avtorId])->one();
        return substr($fl['fizLicoRel']['imya'],0,2).'.'.substr($fl['fizLicoRel']['otchestvo'],0,2).'. '.$fl['fizLicoRel']['familiya'];
    }
    
    public function getAvtorId($avtorId){
        $fl = Polzovatel::find()->where(['id'=>$avtorId])->one();
        return $fl['fiz_lico'];
    }
    
    public function getRoli($prikazId)
    {
        $sql = 'SELECT dok_prikaz_shablon_soglasovanie.porjadok,dok_prikaz_shablon_soglasovanie.dejstvie
          ,dok_prikaz_shablon_ispolnitel.roli_id,dok_prikaz_shablon_ispolnitel.delegirovan_ot_id,dok_prikaz_shablon_ispolnitel.delegirovan_data_nachalo,dok_prikaz_shablon_ispolnitel.delegirovan_data_konec
          ,dok_roli.dolzhnost_id,dok_roli.strukturnoe_podrazdelenie_id,dok_roli.polzovatel_roli,dok_roli.opisanie
        FROM dok_prikaz
          INNER JOIN dok_prikaz_shablon ON dok_prikaz.shablon_id = dok_prikaz_shablon.id
          INNER JOIN dok_prikaz_shablon_soglasovanie ON dok_prikaz_shablon.id = dok_prikaz_shablon_soglasovanie.shablon_id
          INNER JOIN dok_prikaz_shablon_ispolnitel ON dok_prikaz_shablon_soglasovanie.id = dok_prikaz_shablon_ispolnitel.shablon_soglasovanie_id
          INNER JOIN dok_roli ON dok_prikaz_shablon_ispolnitel.roli_id = dok_roli.id
        WHERE dok_prikaz.id = '.$prikazId;
        $res = Yii::$app->db->createCommand($sql)->queryAll();
        $data = ArrayHelper::index($res, 'porjadok');
        return $data;
    }
    
    public function getRabotaBriop($ufl)
    {
        $sql = 'SELECT dnr.dolzhnost AS dolzhnost_id, dnr.strukturnoe_podrazdelenie AS podrazdelenie_id, rfl.fiz_lico AS fl_id
            FROM dolzhnost_fiz_lica_na_rabote as dnr
            INNER JOIN rabota_fiz_lica AS rfl ON dnr.rabota_fiz_lica = rfl.id
            INNER JOIN strukturnoe_podrazdelenie AS sp ON dnr.strukturnoe_podrazdelenie = sp.id
            WHERE sp.organizaciya = 1'.' AND '. 'rfl.fiz_lico = '. $ufl.' AND (dnr.actual = true or dnr.actual IS NULL)';
        $res = Yii::$app->db->createCommand($sql)->queryAll();
        //var_dump($res);
        $data = ArrayHelper::index($res, 'podrazdelenie_id');
        //var_dump($data);die();
        return $res;
    }

    public function getPermission($prikaz_id,$porjadok)
    {
        $p = new Prikaz($prikaz_id);
        $pr = $this->getRoli($prikaz_id);
        
        $u = \Yii::$app->user->identity;
        $ur = $u->roliAsArray;
        $upid = $u->id;
        $ufl = $u->fiz_lico;
        $ujob = $p->getRabotaBriop($ufl);
        //var_dump($ujob);

        $apid = $p->avtorId;
        $afl = $p->getAvtorId($apid);
        $ajob = $p->getRabotaBriop($afl);

        if (!is_null($pr[$porjadok]['polzovatel_roli']) and $upid == $p->avtorId){
            $proli = Rol::asValuesArray($pr[$porjadok]['polzovatel_roli']);
            foreach ($proli as $v){
                if (in_array($v, $ur)) return true;
            }
        }elseif($porjadok == 2 and !is_null($pr[$porjadok]['dolzhnost_id'])){
            foreach ($ujob as $uv){
                foreach ($ajob as $av){
                    if ($pr[$porjadok]['dolzhnost_id'] == $uv['dolzhnost_id'] and $uv['podrazdelenie_id'] == $av['podrazdelenie_id']) return true;
                }
            }
        }elseif(!is_null($pr[$porjadok]['dolzhnost_id']) and !is_null($pr[$porjadok]['strukturnoe_podrazdelenie_id'])){
            foreach ($ujob as $uv){
                if ($pr[$porjadok]['dolzhnost_id'] == $uv['dolzhnost_id'] and $uv['podrazdelenie_id'] == $pr[$porjadok]['strukturnoe_podrazdelenie_id']) return true;
            }
        }elseif(!is_null($pr[$porjadok]['dolzhnost_id'])){
            foreach ($ujob as $uv){
                if ($pr[$porjadok]['dolzhnost_id'] == $uv['dolzhnost_id']) return true;
            }
        }
        return false;
    }
    
    public function getPrikazInfo($prikazId)
    {
        $data =[];
        $q = DokPrikaz::find()->with('shablon')->where(['id' => $prikazId])->asArray()->one();
        $data['shablon_tip'] = $q['shablon']['tip'];
        return $data;
    }
    
    public function getDokId()
    {
        $q = Dok::find()->where(['prikaz_id' => $this->id])->asArray()->one();
        if ($q){
            return ArrayHelper::getValue($q, 'id');
        } else {
            return false;
        }
    }

    public function save()
    {
        $dokPrikaz = new DokPrikaz();
        $dokPrikaz->shablon_id = $this->shablonId;
        $dokPrikaz->avtor_id = $this->avtorId;
        $dokPrikaz->data_sozdanija = date('Y-m-d', strtotime($this->dataSozdanija));
        $dok = new Dok();
        $dokProcess = new DokProcess();
        $dokProcess->sozdal_fiz_lico_id = $this->getAvtorId($this->avtorId);
        $dokProcess->porjadok = 1;
        $dokProcess->data_vnesenija = $dokPrikaz->data_sozdanija;
        $e = false;
        $transaction = \Yii::$app->db->beginTransaction();
            if (!$dokPrikaz->save()) $e = true;
            $this->id = $dokPrikaz->id;
            $dok->prikaz_id = $this->id;
            if (!$dok->save()) $e = true;
            $dokProcess->dok_id = $dok->id;
            $r = $this->getRoli($dokPrikaz->id);
            $dokProcess->roli_id = $r[1]['roli_id'];
            $dokProcess->nazvanie = $r[1]['opisanie'];
            if (!$dokProcess->save()) $e = true;

            foreach ($this['atributy'] as $k => $v) {
                $dokPrikazAtribute = new DokPrikazAtribut();
                $dokPrikazAtribute->prikaz_id = $this->id;
                $dokPrikazAtribute->atribut_id = $k;
                if ($k == 2) {
                    $dokPrikazAtribute->id_znachenija = $v;
                } else {
                    $dokPrikazAtribute->znachenie = $v;
                }
                if (!$dokPrikazAtribute->save()) $e = true;
            }
            foreach ($this['slushateli'] as $v) {
                $dokPrikazTablica = new DokPrikazTablica();
                $dokPrikazTablica->prikaz_id = $this->id;
                $dokPrikazTablica->kurs_fiz_lica_id = $v;
                if (!$dokPrikazTablica->save()) $e = true;
            }
            foreach ($this['komissija'] as $v) {
                $dokPrikazTablica = new DokPrikazTablica();
                $dokPrikazTablica->prikaz_id = $this->id;
                $dokPrikazTablica->fiz_lico_id = $v;
                if (!$dokPrikazTablica->save()) $e = true;
            }
        if (!$e) {
            $transaction->commit();
            return true;
        }else{
            $transaction->rollback();
            return false;
        }
    }
}