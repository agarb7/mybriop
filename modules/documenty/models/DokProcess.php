<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 24.03.2017
 * Time: 22:27
 */
namespace app\modules\documenty\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;


class DokProcess extends ActiveRecord
{
    public function rules()
    {
        return [
            [['dok_id','roli_id','porjadok','nazvanie'], 'required'],
            [['dok_id','roli_id','sozdal_fiz_lico_id','ispolnil_fiz_lico_id'], 'integer'],
            ['komentarij', 'safe'],
            [['data_vnesenija','data_zavershenija'], 'date', 'format' => 'Y-m-d'],
            ['vernut_avtoru', 'boolean'],
        ];
    }

    public function getDok()
    {
        return $this->hasOne(Dok::className(), ['id' => 'dok_id']);
    }

    public function getDokPrikazRel()
    {
        return $this->hasOne(DokPrikaz::className(), ['id' => 'prikaz_id'])->via('dok')->inverseOf;
    }

    public function getDokKomentarii($dokId){
        $sql='SELECT dok_process.id, dok_process.data_vnesenija, fiz_lico.familiya||\' \'||fiz_lico.imya||\' \'||fiz_lico.otchestvo AS fio, dok_process.komentarij
FROM dok_process
INNER JOIN fiz_lico ON dok_process.sozdal_fiz_lico_id = fiz_lico.id
WHERE dok_process.dok_id ='.$dokId.' AND dok_process.komentarij NOTNULL
ORDER BY dok_process.id';
        $res = Yii::$app->db->createCommand($sql)->queryAll();
        $kom = ArrayHelper::index($res, 'id');
        //var_dump($kom);
        $komentarii =[];
        foreach ($kom as $k=>$v){
            $komentarii[$k]=['date' => $v['data_vnesenija'],'fio'=>$v['fio'],'komentarij'=>$v['komentarij']];
        }
        return $komentarii;
    }
    
    // Список процессов требующих обработки
    public function getProcess()
    {
        $sql='SELECT p.id,p.dok_id,p.roli_id,p.sozdal_fiz_lico_id,p.ispolnil_fiz_lico_id,p.porjadok,p.nazvanie,p.komentarij,p.data_vnesenija,p.data_zavershenija,p.vernut_avtoru,dok.prikaz_id
FROM (SELECT max(id) AS pid, dok_id FROM dok_process GROUP BY dok_id) AS lp
INNER JOIN dok_process AS p ON p.dok_id=lp.dok_id AND lp.pid=p.id
INNER JOIN dok ON p.dok_id=dok.id';
        $res = Yii::$app->db->createCommand($sql)->queryAll();
        $processy = ArrayHelper::index($res, 'dok_id');
        $process = [];
        foreach ($processy as $k => $v)
        {
            if (!is_null($v['prikaz_id'])){
                $prikaz = new Prikaz($v['prikaz_id']);
                if ($prikaz-> getPermission($v['prikaz_id'],$v['porjadok']) and $prikaz->statusPodpisan == 0){
                    $process[$k]['process_id'] = $v['id'];
                    $process[$k]['dok_id'] = $k;
                    $process[$k]['dok_tip'] = 'Приказ';
                    $o = $prikaz->getPrikazInfo($v['prikaz_id']);
                    $process[$k]['sozdal_fio'] = $prikaz->getAvtor($prikaz['avtorId']);
                    $process[$k]['opisanie'] = $o['shablon_tip'];
                    $process[$k]['data_sozdanija'] = $prikaz->dataSozdanija;
                    //$process[$k]['komentarij'] = $processy[$k]['komentarij'];
                    $process[$k]['komentarij'] = $this->getDokKomentarii($k);
                    $process[$k]['pid'] = $v['prikaz_id'];
                    $process[$k]['dejstvie'] = $v['nazvanie'];
                }
            }elseif(false){ 
                
            }
        }
        return $process;
    }

    // Следующий процесс
    public function getNextProcess($oldid)
    {
        $oldp = $this->find()->with('dok')->where(['id' => $oldid])->asArray()->one();
        //var_dump($oldp );die();
        if (!is_null($oldp['dok']['prikaz_id'])){
            $n = $oldp['porjadok']+1;
            $sql = 'SELECT dok_prikaz_shablon_soglasovanie.porjadok,dok_prikaz_shablon_soglasovanie.dejstvie
              ,dok_prikaz_shablon_ispolnitel.roli_id,dok_prikaz_shablon_ispolnitel.delegirovan_ot_id,dok_prikaz_shablon_ispolnitel.delegirovan_data_nachalo,dok_prikaz_shablon_ispolnitel.delegirovan_data_konec
              ,dok_roli.dolzhnost_id,dok_roli.strukturnoe_podrazdelenie_id,dok_roli.polzovatel_roli,dok_roli.opisanie
            FROM dok_prikaz
              INNER JOIN dok_prikaz_shablon ON dok_prikaz.shablon_id = dok_prikaz_shablon.id
              INNER JOIN dok_prikaz_shablon_soglasovanie ON dok_prikaz_shablon.id = dok_prikaz_shablon_soglasovanie.shablon_id
              INNER JOIN dok_prikaz_shablon_ispolnitel ON dok_prikaz_shablon_soglasovanie.id = dok_prikaz_shablon_ispolnitel.shablon_soglasovanie_id
              INNER JOIN dok_roli ON dok_prikaz_shablon_ispolnitel.roli_id = dok_roli.id
              INNER JOIN dok_prikaz_atribut ON dok_prikaz.id = dok_prikaz_atribut.prikaz_id
            WHERE dok_prikaz.id = '.$oldp['dok']['prikaz_id'].' AND dok_prikaz_shablon_soglasovanie.porjadok = '.$n;
            $res = Yii::$app->db->createCommand($sql)->queryOne();
            $res['dok_id'] = $oldp['dok']['id'];
            if (!empty($res)) $data = $res;
        }
        return $data;
    }
}