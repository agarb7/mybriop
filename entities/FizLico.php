<?php

namespace app\entities;

use app\transformers\DateTransformer;
use app\enums\KategoriyaPedRabotnika;
use app\helpers\StringHelper;
use app\transformers\EnumTransformer;
use app\transformers\PasportKemVydanKodTransformer;
use app\transformers\SnilsTransformer;
use app\transformers\TelefonTransformer;
use yii\db\ActiveQuery;

/**
 * Class FizLico
 * @package app\models\entities
 *
 * @property int $id
 * @property string $familiya
 * @property string $imya
 * @property string $otchestvo
 * @property string $dataRozhdeniya
 * @property string $pasportNo
 * @property string $pasportKemVydanKod
 * @property string $pasportKemVydan
 * @property string $pasportKogdaVydan
 * @property string $inn
 * @property string $snils
 * @property string $telefon
 * @property string $email
 * @property string $propiska_adresnyj_objekt bigint,
 * @property string $propiskaDom
 * @property string $propiskaKvartira
 * @property string $pedStazh
 * @property string $kategoriyaPedRabotnika
 * @property string $formattedTelefon
 * @property string $pasportKemVydanKodFormatted
 * @property string $snilsFormatted
 * @property \DateTime $dataRozhdeniyaAsDate
 * @property \DateTime $pasportKogdaVydanAsDate
 */

class FizLico extends EntityBase
{
    public $statusKursaGdeSlushatel;

    public function transformations()
    {
        return [
            ['kategoriyaPedRabotnikaAsEnum' => 'kategoriya_ped_rabotnika', EnumTransformer::className(),['enum' => KategoriyaPedRabotnika::className()]],
            ['formattedTelefon' => 'telefon', TelefonTransformer::className()],
            ['pasportKemVydanKodFormatted' => 'pasport_kem_vydan_kod', PasportKemVydanKodTransformer::className()],
            ['snilsFormatted' => 'snils', SnilsTransformer::className()],
            ['dataRozhdeniyaAsDate' => 'data_rozhdeniya', DateTransformer::className()],
            ['pasportKogdaVydanAsDate' => 'pasport_kogda_vydan', DateTransformer::className()]
        ];
    }

    public function getFamiliyaInicialy()
    {
        $result = '';

        if ($this->familiya)
            $result = $this->familiya;

        if ($this->imya) {
            if ($result)
                $result .= StringHelper::nbsp();
            $result .= mb_substr($this->imya, 0, 1) . '.';
        }

        if ($result && $this->otchestvo)
            $result .= StringHelper::nbsp() . mb_substr($this->otchestvo, 0, 1) . '.';

        return $result;
    }

    public function getFio()
    {
        $fio = [];
        if ($this->familiya)
            $fio[] = $this->familiya;

        if ($this->imya) {
            $fio[] = $this->imya;

            if ($this->otchestvo)
                $fio[] = $this->otchestvo;
        }

        return implode(' ', $fio);
    }

    public function getPasportSeriyaFormatted()
    {
        return substr($this->pasportNo, 0, 2) . ' ' . substr($this->pasportNo, 2, 2);
    }

    public function getPasportNomer()
    {
        return substr($this->pasportNo, 4, 10);
    }

    public function getPolzovatelRel()
    {
        return $this->hasOne(Polzovatel::className(), ['fiz_lico' => 'id'])->inverseOf('fizLicoRel');
    }

    public function getRabotyFizLicaRel()
    {
        return $this->hasMany(RabotaFizLica::className(), ['fiz_lico' => 'id'])->inverseOf('fizLicoRel');
    }

    public function getObrazovaniyaFizLicaRel()
    {
        return $this->hasMany(ObrazovanieFizLica::className(), ['fiz_lico' => 'id'])->inverseOf('fizLicoRel');
    }

    public function getKursyFizLicaRel()
    {
        return $this->hasMany(KursFizLica::className(), ['fiz_lico' => 'id'])->inverseOf('fizLicoRel');
    }

    public function getKursySlushatelyaRel()
    {
        return $this
            ->hasMany(Kurs::className(), ['id' => 'kurs'])
            ->via('kursyFizLicaRel');
    }

    public function getKursyRukovoditelyaRel()
    {
        return $this->hasMany(Kurs::className(), ['rukovoditel' => 'id'])->inverseOf('rukovoditelRel');
    }

    public function getTekuschayaAttestaciyaFizLicaRel()
    {
        return $this->hasOne(TekuschayaAttestaciyaFizLica::className(), ['fiz_lico' => 'id'])->inverseOf('fizLicoRel');
    }

    public function getAttestaciyaFizLicaRel()
    {
        return $this
            ->hasOne(AttestaciyaFizLica::className(), ['id' => 'attestaciya_fiz_lica'])
            ->via('tekuschayaAttestaciyaFizLicaRel');
    }

    public function getStazhiFizLicaRel()
    {
        return $this
            ->hasMany(StazhFizLica::className(), ['fiz_lico' => 'id'])
            ->inverseOf('fizLicoRel');
    }

    public function getRabotnikAttestacionnojKomissiiRel(){
        return $this->hasOne(RabotnikAttestacionnojKomissii::className(),['fiz_lico'=>'id'])->inverseOf('fizLicoRel');
    }

    public function getZayavlenieNaAttestaciyuRel(){
        return $this->hasMany(ZayavlenieNaAttestaciyu::className(),['fiz_lico'=>'id'])->inverseOf('fizLicoRel');
    }

    /**
     * @param $fizLicoId -  идентификатор физ лица
     * @return string[]:
     * familiya - фамилия
     * imya - имя
     * otchestvo - отчество
     * fio - полное ФИО
     */
    public static function getFioById($fizLicoId)
    {
        $fizLico = FizLico::find()->select(['familiya','imya','otchestvo'])->where(['id'=>$fizLicoId])->one();
        if ($fizLico)
            return [
                'familiya' => $fizLico->familiya,
                'imya' => $fizLico->imya,
                'otchestvo' => $fizLico->otchestvo,
                'fio' => $fizLico->familiya.' '.$fizLico->imya.' '.$fizLico->otchestvo
            ];
        else
            return [];
    }

    public static function getEmailById($fizLicoId){
        $fizLico = FizLico::find()->select(['email'])->where(['id'=>$fizLicoId])->one();
        if ($fizLico)
            return $fizLico->email;
        else
            return false;
    }

    /**
     * @return EntityQuery
     */
    public static function findRukovoditeliKursov()
    {
        return static::find()->innerJoinWith('kursyRukovoditelyaRel');
    }

    public static function findSlushateliKursa($kurs)
    {
        return static::find()
            ->innerJoinWith([
                'kursySlushatelyaRel' => function ($q) use ($kurs) {
                    /**
                     * @var $q ActiveQuery
                     */
                    $q->onCondition(['kurs.id' => $kurs]);
                }
            ])
            ->select([
                '{{fiz_lico}}.*',
                '{{kurs_fiz_lica}}.[[status]] as [[statusKursaGdeSlushatel]]'
            ]);
    }

}