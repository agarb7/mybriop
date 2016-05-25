<?php
namespace app\entities;

use app\enums\TipDokumentaObObrazovanii;
use app\transformers\DateTransformer;
use Yii;

/**
 * Class ObrazovanieFizLica
 * @package app\entities
 * @property int id
 * @property string dokument_ob_obrazovanii_tip
 * @property string dokument_ob_obrazovanii_seriya
 * @property string dokument_ob_obrazovanii_nomer
 * @property string dokument_ob_obrazovanii_data
 * @property int kvalifikaciya
 * @property int organizaciya
 * @property int dokument_ob_obrazovanii_kopiya
 * @property string kurs_tip
 * @property string kurs_nazvanie
 * @property int kurs_chasy
 * @property \DateTime dokumentObObrazovaniiDataAsDate
 */
class ObrazovanieFizLica extends EntityBase
{
    public function transformations()
    {
        return [
            ['dokumentObObrazovaniiDataAsDate' => 'dokument_ob_obrazovanii_data', DateTransformer::className()],
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            $this->dokument_ob_obrazovanii_seriya = trim($this->dokument_ob_obrazovanii_seriya);
            $this->dokument_ob_obrazovanii_nomer = trim($this->dokument_ob_obrazovanii_nomer);
            return true;
        }
        else{
            return false;
        }
    }

    public function getDokumentObObrazovaniiSummary()
    {
        $formatter = Yii::$app->formatter;

        $res = TipDokumentaObObrazovanii::getNameBySql($this->dokument_ob_obrazovanii_tip);

        if ($this->dokument_ob_obrazovanii_seriya || $this->dokument_ob_obrazovanii_nomer) {
            $res .= ' №';
            if ($this->dokument_ob_obrazovanii_seriya)
                $res .= ' ' . $formatter->asText($this->dokument_ob_obrazovanii_seriya);
            if ($this->dokument_ob_obrazovanii_nomer)
                $res .= ' ' . $formatter->asText($this->dokument_ob_obrazovanii_nomer);
        }

        if ($this->dokument_ob_obrazovanii_data) {
            $res .= ' выдан ' . $formatter->asDate($this->dokument_ob_obrazovanii_data);
        }

        return $res;
    }

    public function getFizLicoRel()
    {
        return $this->hasOne(FizLico::className(),['id'=>'fiz_lico'])->inverseOf('obrazovaniyaFizLicaRel');
    }

    public function getObrazovanieDlyaZayavleniyaNaAttestaciyuRel(){
        return $this->hasOne(ObrazovanieDlyaZayavleniyaNaAttestaciyu::className(),['id'=>'obrazovanie_istochnik'])->inverseOf('obrazovanieFizLicaRel');
    }

    public function getOrganizaciyaRel()
    {
        return $this->hasOne(Organizaciya::className(), ['id'=>'organizaciya'])->inverseOf('obrazovaniyaFizLicaRel');
    }

    public function getKvalifikaciyaRel()
    {
        return $this->hasOne(Kvalifikaciya::className(), ['id'=>'kvalifikaciya'])->inverseOf('obrazovaniyaFizLicaRel');
    }
}