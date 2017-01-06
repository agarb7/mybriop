<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 06.09.15
 * Time: 13:36
 */

namespace app\entities;

/**
 * Class ObrazovanieDlyaZayavleniyaNaAttestaciyu
 * @package app\entities
 * @property int  id
 * @property int zayavlenie_na_attestaciyu
 * @property int kurs_istochnik
 * @property int obrazovanie_istochnik
 * @property int kurs_tip
 * @property int kurs_nazvanie
 * @property int kurs_chasy
 * @property int dokument_ob_obrazovanii_tip
 * @property int dokument_ob_obrazovanii_seriya
 * @property int dokument_ob_obrazovanii_nomer
 * @property int dokument_ob_obrazovanii_data
 * @property int dokument_ob_obrazovanii_kopiya
 * @property int kvalifikaciya
 * @property int organizaciya
 */

class ObrazovanieDlyaZayavleniyaNaAttestaciyu extends EntityBase
{

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            $this->dokument_ob_obrazovanii_seriya = $this->dokument_ob_obrazovanii_seriya ? mb_substr(trim($this->dokument_ob_obrazovanii_seriya),0,40) : null;
            $this->dokument_ob_obrazovanii_nomer = $this->dokument_ob_obrazovanii_nomer ? mb_substr(trim($this->dokument_ob_obrazovanii_nomer),0,40) : null;
            return true;
        }
        else{
            return false;
        }
    }

    public function getObrazovanieFizLicaRel(){
        return $this->hasOne(ObrazovanieFizLica::className(),['obrazovanie_istochnik'=>'id'])->inverseOf('obrazovanieDlyaZayavleniyaNaAttestaciyuRel');
    }

    public function getZayavleniyaNaAttestaciyuObrazovanieRel(){
        return $this->hasMany(ZayavlenieNaAttestaciyu::className(),['id'=>'zayavlenie_na_attestaciyu'])->andOnCondition(['kurs_tip'=>null])->inverseOf('obrazovaniyaRel');
    }

    public function getOrganizaciyaRel(){
        return $this->hasOne(Organizaciya::className(),['id'=>'organizaciya'])
            ->from(Organizaciya::tableName().' obr_organizaciya')
            ->inverseOf('obrazovaniyaDlyaZayavleniyaNaAttestaciyuRel');
    }

    public function getObrazovanieOrganizaciyaRel(){
        return $this->hasOne(Organizaciya::className(),['id'=>'organizaciya'])
            ->from(Organizaciya::tableName().' obrazovanie_organizaciya');
    }

    public function getKursOrganizaciyaRel(){
        return $this->hasOne(Organizaciya::className(),['id'=>'organizaciya'])
            ->from(Organizaciya::tableName().' kurs_organizaciya');
    }

    public function getKvalifikaciyaRel()
    {
        return $this->hasOne(Kvalifikaciya::className(),['id'=>'kvalifikaciya'])
            ->from(Kvalifikaciya::tableName().' obr_kvalifikaciya')
            ->inverseOf('obrazovanieDlyaZayavleniyaNaAttestaciyuRel');
    }

    public function getObrazovanieKvalifikaciyaRel()
    {
        return $this->hasOne(Kvalifikaciya::className(),['id'=>'kvalifikaciya'])
            ->from(Kvalifikaciya::tableName().' obrazovanie_kvalifikaciya');
    }

    public function getKursKvalifikaciyaRel()
    {
        return $this->hasOne(Kvalifikaciya::className(),['id'=>'kvalifikaciya'])
            ->from(Kvalifikaciya::tableName().' kurs_kvalifikaciya');
    }

    public function getFajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'dokument_ob_obrazovanii_kopiya'])
            ->from(Fajl::tableName().' obr_dlya_zayavleniya_fajl')
            ->inverseOf('obrazovanieDlyaZayavleniyaNaAttestaciyuRel');
    }

    public function getObrazovanieFajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'dokument_ob_obrazovanii_kopiya'])
            ->from(Fajl::tableName().' obr_dlya_zayavleniya_obr_fajl');
    }

    public function getKursFajlRel(){
        return $this->hasOne(Fajl::className(),['id'=>'dokument_ob_obrazovanii_kopiya'])
            ->from(Fajl::tableName().' obr_dlya_zayavleniya_kurs_fajl');
    }

    public function getZayavleniyaNaAttestaciyuKursyRel(){
        return $this->hasMany(ZayavlenieNaAttestaciyu::className(),['id'=>'zayavlenie_na_attestaciyu'])->andOnCondition(['is not','kurs_tip',null])->inverseOf('kursyRel');
    }

    public function rules(){
        return[
          ['zayavlenie_na_attestaciyu','required'],
          [[
              'kurs_istochnik','obrazovanie_istochnik','kurs_tip',
              'kurs_nazvanie','kurs_chasy','dokument_ob_obrazovanii_tip',
              'dokument_ob_obrazovanii_seriya','dokument_ob_obrazovanii_nomer',
              'dokument_ob_obrazovanii_data','dokument_ob_obrazovanii_kopiya',
              'kvalifikaciya','organizaciya'
           ],'safe']
        ];
    }
}

