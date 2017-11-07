<?php
namespace app\modules\documenty\models;

use yii\db\ActiveRecord;
use app\validators\DateValidator;
use app\entities\Polzovatel;
use app\entities\FizLico;

class DokPrikaz extends ActiveRecord
{
    public function rules()
    {
        return [
            [['shablon_id', 'avtor_id', 'data_sozdanija'],'required'],
            ['status_podpisan', 'integer'],
            ['nomer_registracii', 'string', 'max' => 10],
            [['data_registracii', 'data_sozdanija'], 'date', 'format' => 'Y-m-d'],
        ];
    }
    
    public function getPolzovatel()
    {
        return $this->hasOne(Polzovatel::className(), ['id' => 'avtor_id']);
    }
    
    public function getFizLicoRel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'fiz_lico'])->via('polzovatel');
    }

    public function getShablon()
    {
        return $this->hasOne(DokPrikazShablon::className(), ['id' => 'shablon_id']);
    }
    
    public function getZaregistrirovannyePrikazy()
    {
        $q = $this->find()
            ->innerJoin('dok', 'dok.prikaz_id = dok_prikaz.id')
            ->where(['dok_prikaz.status_podpisan' => 1, 'dok.actual' => true])->asArray()->orderBy('id')->all();
        $zp = [];
        foreach ($q as $v){
            $p = new Prikaz($v['id']);
            $o = $p->getPrikazInfo($v['id']);
            $zp[$v['id']]['pid'] = $v['id'];
            $zp[$v['id']]['nomer_registracii'] = $v['nomer_registracii'];
            $zp[$v['id']]['data_registracii'] = $v['data_registracii'];
            $zp[$v['id']]['opisanie'] = $o['shablon_tip'];
            $zp[$v['id']]['avtor'] = $p->getAvtor($v['avtor_id']);
            $zp[$v['id']]['data_sozdanija'] = $v['data_sozdanija'];
        }
        return $zp;
    }
}