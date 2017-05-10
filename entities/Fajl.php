<?php
namespace app\entities;
use yii\bootstrap\Html;

/**
 * Fail record
 * @property integer $id
 * @property string $vneshnee_imya_fajla
 * @property string $vnutrennee_imya_fajla
 * @property integer $vladelec
 */
class Fajl extends EntityBase
{
    public function rules()
    {
        return [
            [['id','vneshnee_imya_fajla','vnutrennee_imya_fajla','vladelec'],'safe']
        ];
    }

    public function getUri()
    {
        return '/data/' . $this->vladelec . '/' . $this->vnutrennee_imya_fajla;
    }

    public function getZayavlenieNaAttestaciyuKopiyaAttestacionnogoListaRel()
    {
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['attestaciya_kopiya_attestacionnogo_lista'=>'id'])->inverseOf('attestaciyaFajlRel');
    }

    public function getZayavlenieNaAttestaciyuKopiyaTrudovoiRel()
    {
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['rabota_kopiya_trudovoj_knizhki'=>'id'])->inverseOf('kopiyaTruidovoiajlRel');
    }

    public function getObrazovanieDlyaZayavleniyaNaAttestaciyuRel()
    {
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['dokument_ob_obrazovanii_kopiya'=>'id'])->inverseOf('fajlRel');
    }

    public function getZayavlenieNaAttestaciyuVarIsp2FajlRel()
    {
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['var_ispytanie_2_fajl'=>'id'])->inverseOf('varIspytanie2FajlRel');
    }

    public function getZayavlenieNaAttestaciyuVarIsp3FajlRel()
    {
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['var_ispytanie_3_fajl'=>'id'])->inverseOf('varIspytanie3FajlRel');
    }

    public function getZayavlenieNaAttestaciyuPortfolioRel(){
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['portfolio'=>'id'])->inverseOf('portfolioFajlRel');
    }

    public function getZayavlenieNaAttestaciyuPrezentatsiyaRel()
    {
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['prezentatsiya'=>'id'])->inverseOf('prezentatsiyaFajlRel');
    }

    public function getZayavlenieNaAttestaciyuSvedeniyaOSebeRel(){
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['svedeniya_o_sebe_fajl'=>'id'])->inverseOf('svedeniyaOSebeFajlRel');
    }

    public function getZayavlenieNaAttestaciyuInformacionnajaKartaRel(){
        return $this->hasOne(ZayavlenieNaAttestaciyu::className(),['informacionnaja_karta'=>'id'])->inverseOf('informacionnajaKartaFajlRel');
    }

    public function getFileSpan()
    {
        return Html::tag('span',$this->vneshnee_imya_fajla,['class'=>'file_item','data-file-id'=>$this->id]);
    }

    public function getFileLink($class = false)
    {
        if (!$class) $class = '';
        return Html::a($this->vneshnee_imya_fajla, $this->getUri() ,['class'=>'file_item '.$class,'data-file-id'=>$this->id]);
    }

    /**
     * @param $id - file id
     * @return string
     * @throws \Exception - if $id is bad paramter
     */
    public static function getFileUrl($id){
        if (!$id || !is_numeric($id)){
            throw new \Exception('Параметр $id задан неверно');
            return 0;
        }
        $file = static::findOne($id);
        return $file->getUri();
    }
}