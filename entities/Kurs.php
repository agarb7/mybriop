<?php
namespace app\entities;

use app\components\FuncResponse;
use app\transformers\DateTransformer;
use app\transformers\EnumArrayTransformer;
use app\enums\FormaObucheniya;
use app\enums2\StatusKursaFizLica;
use app\enums\TipFinansirovaniya;
use app\transformers\EnumNamesArrayTransformer;
use app\transformers\EnumNameTransformer;
use app\transformers\EnumTransformer;
use Yii;

/**
 * Class Kurs
 * @package app\entities
 *
 * @property integer $id
 * @property string $nazvanie
 * @property string $annotaciya
 * @property string $tip
 * @property string $prisvaemayaKvalifikaciya
 * @property string $aktualnost
 * @property string $cel
 * @property string $zadachi
 * @property string $planiruemyeRezultaty
 * @property string $harakteristikaNovojKvalifikacii
 * @property string $trebovaniya_k_urovnyu_podgotovki
 * @property string $formaObucheniya
 * @property string $informacionnyeUsloviya
 * @property string $kadrovyeUsloviya
 * @property string $uchebnometodicheskieUsloviya
 * @property string $tehnicheskieUsloviya
 * @property string $itogovayaAttestaciya
 * @property string $rezhimZanyatij
 * @property string $spisokLiteratury
 * @property string $formaItogovojAttestacii
 * @property integer $chasyItogovojAttestacii
 * @property string $opisanieItogovojAttestacii
 * @property string $nedelyaItogovojAttestacii
 * @property string $finansirovanie
 * @property string $strukturnoePodrazdelenie
 * @property integer $rukovoditel
 * @property string $raschitanoChasov
 * @property string $raschitanoSlushatelej
 * @property integer $maksimalnoSlushatelej
 * @property string $formyObucheniya
 * @property string $ochnoeNachalo
 * @property string $ochnoeKonec
 * @property string $zaochnoeNachalo
 * @property string $zaochnoeKonec
 * @property string $statusProgrammy
 * @property string $sostaviteli
 * @property string $recenzenti
 * @property string $itogovayaAttestaciyaTekst
 *
 * @property array $formyObucheniyaAsArray
 * @property string[] $formyObucheniyaAsNames
 *
 * @property \DateTime $ochnoeNachaloAsDate
 * @property \DateTime $ochnoeKonecAsDate
 * @property \DateTime $zaochnoeNachaloAsDate
 * @property \DateTime $zaochnoeKonecAsDate
 * @property \DateTime $nachaloAsDate
 * @property \DateTime $konecAsDate
 */
class Kurs extends EntityBase
{
    public function transformations()
    {
        return [
            ['formyObucheniyaAsArray' => 'formy_obucheniya', EnumArrayTransformer::className(), ['enum' => FormaObucheniya::className()]],
            ['formyObucheniyaAsNames' => 'formy_obucheniya', EnumNamesArrayTransformer::className(), ['enum' => FormaObucheniya::className()]],

            ['finansirovanieAsName' => 'finansirovanie', EnumNameTransformer::className(), ['enum' => TipFinansirovaniya::className()]],
            ['finansirovanieAsEnum' => 'finansirovanie', EnumTransformer::className(), ['enum' => TipFinansirovaniya::className()]],

            ['ochnoeNachaloAsDate' => 'ochnoe_nachalo', DateTransformer::className()],
            ['ochnoeKonecAsDate' => 'ochnoe_konec', DateTransformer::className()],
            ['zaochnoeNachaloAsDate' => 'zaochnoe_nachalo', DateTransformer::className()],
            ['zaochnoeKonecAsDate' => 'zaochnoe_konec', DateTransformer::className()],
        ];
    }

    public function getNachaloAsDate()
    {
        return $this->nullMin($this->ochnoeNachaloAsDate, $this->zaochnoeNachaloAsDate);
    }

    public function getKonecAsDate()
    {
        return $this->nullMax($this->ochnoeKonecAsDate, $this->zaochnoeKonecAsDate);
    }

    public function getSrokProvedeniyaFormatted()
    {
        $formatter = Yii::$app->formatter;

        $res = null;
        if ($nach = $this->getNachaloAsDate())
            $res .= 'c ' . $formatter->asDate($nach);

        if ($kon = $this->getKonecAsDate()) {
            if ($res) $res .= ' ';
            $res .= 'по ' . $formatter->asDate($kon);
        }

        return $res;
    }

    public function getRukovoditelRel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'rukovoditel'])->inverseOf('kursyRukovoditelyaRel');
    }

    public function getKategoriiSlushatelejRel()
    {
        return $this
            ->hasMany(KategoriyaSlushatelya::className(), ['id' => 'kategoriya_slushatelya'])
            ->viaTable('kategoriya_slushatelya_kursa', ['kurs' => 'id']);
    }

    public function getKursyFizLicaRel()
    {
        return $this->hasMany(KursFizLica::className(), ['kurs' => 'id'])->inverseOf('kursRel');
    }

    public function getSlushateliRel()
    {
        return $this
            ->hasMany(FizLico::className(), ['id' => 'fiz_lico'])
            ->via('kursyFizLicaRel', function ($q) {
                $q->onCondition(['{{kurs_fiz_lica}}.[[status]]' => StatusKursaFizLica::ZAPISAN]);
            });
    }

    public function getRazdelyKursaRel()
    {
        return $this->hasMany(RazdelKursa::className(), ['kurs' => 'id'])->inverseOf('kursRel');
    }

    private function nullMin($l, $r)
    {
        if ($l===null)
            return $r;

        if ($r===null)
            return $l;

        return min($l, $r);
    }

    private function nullMax($l, $r)
    {
        if ($l===null)
            return $r;

        if ($r===null)
            return $l;

        return max($l, $r);
    }

    public function getKimRel()
    {
        return $this
            ->hasMany(Kim::className(), ['id' => 'kim'])
            ->viaTable('kim_kursa', ['kurs' => 'id']);
    }

    public function getUmkRel()
    {
        return $this
            ->hasMany(Umk::className(), ['id' => 'umk'])
            ->viaTable('umk_kursa', ['kurs' => 'id']);
    }

    public function getFormaItogovojAttestaciiKursaRel()
    {
        return $this
            ->hasOne(FormaItogovojAttestaciiKursa::className(),
                ['id' => 'forma_itogovoj_attestacii']);
    }

    public function getTemyDiplomnihRabotRel(){
        return $this
            ->hasMany(TemaDiplomnojRabotyKursa::className(),['kurs'=>'id']);
    }

    public function getKontroliruyushihKursaRel(){
        return $this
            ->hasMany(KontroliruyuschijKursa::className(),['kurs'=>'id']);
    }

    /**
     * @param $kurs_id
     * @return FuncResponse
     * @throws \Exception
     * @throws \yii\db\Exception
     */
    public static function deleteProgram($kurs_id){
        $result = new FuncResponse();
        /**
         * @var Kurs $kurs
         */
        $kurs = Kurs::find()
            ->joinWith('kimRel')
            ->joinWith('umkRel')
            ->joinWith('kontroliruyushihKursaRel')
            ->joinWith('temyDiplomnihRabotRel')
            ->joinWith('razdelyKursaRel.podrazdelyKursaRel.temyRel')
            ->joinWith('razdelyKursaRel.podrazdelyKursaRel.kimRel')
            ->joinWith('razdelyKursaRel.podrazdelyKursaRel.umkRel')
            ->joinWith('razdelyKursaRel.podrazdelyKursaRel.kontroliruyushihPodrazdelaKursaRel')
            ->joinWith('razdelyKursaRel.podrazdelyKursaRel.temyRel.kimRel')
            ->joinWith('razdelyKursaRel.podrazdelyKursaRel.temyRel.umkRel')
            ->where(['kurs.id' => $kurs_id])
            ->one();

        $transaction = \Yii::$app->db->beginTransaction();
        try{
            foreach ($kurs->razdelyKursaRel as $razdel) {
                /**
                 * @var RazdelKursa $razdel
                 */
                foreach ($razdel->podrazdelyKursaRel as $podrazdel) {
                    /**
                     * @var PodrazdelKursa $podrazdel
                     */
                    //temy
                    foreach ($podrazdel->temyRel as $tema) {
                        /**
                         * @var Tema $tema
                         */
                        //kim_temy
                        foreach ($tema->kimRel as $item) {
                            KimTemy::deleteAll(['tema'=>$tema->id,'kim'=>$item->id]);
                            if (!$item->isUsed()) $item->delete();
                        }
                        //umk_temy
                        foreach ($tema->umkRel as $item) {
                            UmkTemy::deleteAll(['tema'=>$tema->id,'umk'=>$item->id]);
                            if (!$item->isUsed()) $item->delete();
                        }
                        $tema->delete();
                    }
                    //kim_podrazdela
                    foreach ($podrazdel->kimRel as $item) {
                        KimPodrazdelaKursa::deleteAll(['podrazdel_kursa'=>$podrazdel->id,'kim'=>$item->id]);
                        if (!$item->isUsed()) $item->delete();
                    }
                    //umk_podrazdela
                    foreach ($podrazdel->umkRel as $item) {
                        UmkPodrazdelaKursa::deleteAll(['podrazdel_kursa'=>$podrazdel->id,'umk'=>$item->id]);
                        if (!$item->isUsed()) $item->delete();
                    }
                    //kontroliruyushie
                    foreach ($podrazdel->kontroliruyushihPodrazdelaKursaRel as $kontroliruyushij) {
                        /**
                         * @var KontroliruyuschijPodrazdelaKursa $kontroliruyushij
                         */
                        $kontroliruyushij->delete();
                    }
                    $podrazdel->delete();
                }
                $razdel->delete();
            }
            //kim kurs
            foreach ($kurs->kimRel as $item) {
                KimKursa::deleteAll(['kurs'=>$kurs->id,'kim'=>$item->id]);
                if (!$item->isUsed()) $item->delete();
            }
            //umk kurs
            foreach ($kurs->umkRel as $item) {
                UmkKursa::deleteAll(['kurs'=>$kurs->id,'umk'=>$item->id]);
                if (!$item->isUsed()) $item->delete();
            }
            //kontroliruyushie
            foreach ($kurs->kontroliruyushihKursaRel as $kontroliruyushij) {
                /**
                 * @var KontroliruyuschijKursa $kontroliruyushij
                 */
                $kontroliruyushij->delete();
            }
            $kurs->annotaciya = null;
            $kurs->aktualnost = null;
            $kurs->cel = null;
            $kurs->zadachi = null;
            $kurs->zadachi = null;
            $kurs->planiruemyeRezultaty = null;
            $kurs->formaItogovojAttestacii = null;
            $kurs->harakteristikaNovojKvalifikacii = null;
            $kurs->trebovaniya_k_urovnyu_podgotovki = null;
            $kurs->formaObucheniya = null;
            $kurs->informacionnyeUsloviya = null;
            $kurs->kadrovyeUsloviya = null;
            $kurs->uchebnometodicheskieUsloviya = null;
            $kurs->tehnicheskieUsloviya = null;
            $kurs->itogovayaAttestaciya = null;
            $kurs->rezhimZanyatij = null;
            $kurs->spisokLiteratury = null;
            $kurs->chasyItogovojAttestacii = null;
            $kurs->opisanieItogovojAttestacii = null;
            $kurs->nedelyaItogovojAttestacii = null;
            $kurs->harakteristikaNovojKvalifikacii = null;
            $kurs->sostaviteli = null;
            $kurs->recenzenti = null;
            $kurs->itogovayaAttestaciyaTekst = null;
            $kurs->save();
            $transaction->commit();
        }
        catch(Exception $e){
            $transaction->rollBack();
            $result->type = FuncResponse::ERROR;
            $result->msg = $e->getMessage();
        }
        return $result;
    }

    public static function doesHaveProgram($kurs_id){
        /**
         * @var Kurs $kurs
         */
        $kurs = Kurs::find()
            ->joinWith('razdelyKursaRel')
            ->joinWith('kimRel')
            ->joinWith('umkRel')
            ->joinWith('kontroliruyushihKursaRel')
            ->joinWith('temyDiplomnihRabotRel')
            ->where(['kurs.id' => $kurs_id])
            ->one();

        if ($kurs->annotaciya != null or $kurs->aktualnost != null or $kurs->cel != null or
            $kurs->zadachi != null or $kurs->zadachi != null or $kurs->planiruemyeRezultaty != null or
            $kurs->formaItogovojAttestacii != null or $kurs->harakteristikaNovojKvalifikacii != null or
            $kurs->trebovaniya_k_urovnyu_podgotovki != null or $kurs->formaObucheniya != null or
            $kurs->informacionnyeUsloviya != null or $kurs->kadrovyeUsloviya != null or
            $kurs->uchebnometodicheskieUsloviya != null or $kurs->tehnicheskieUsloviya != null or
            $kurs->itogovayaAttestaciya != null or $kurs->rezhimZanyatij != null or $kurs->spisokLiteratury != null or
            $kurs->chasyItogovojAttestacii != null or $kurs->opisanieItogovojAttestacii != null or
            $kurs->nedelyaItogovojAttestacii != null or $kurs->harakteristikaNovojKvalifikacii != null or
            $kurs->sostaviteli != null or $kurs->recenzenti != null or $kurs->itogovayaAttestaciyaTekst != null or
            count($kurs->razdelyKursaRel) > 0  or count($kurs->kimRel) > 0 or count($kurs->umkRel) > 0 or
            count($kurs->kontroliruyushihKursaRel) > 0 or count($kurs->temyDiplomnihRabotRel) > 0
        )
            return true;
        else
            return false;
    }

    public static function isVariativnijRazdelHasError($kurs_id){
        //todo do it in objects!!!
        $sql = 'select pk.id, sum(coalesce(t.chasy,0)) as chasy,
                  sum(case when t.tip_raboty=1 then t.chasy else 0  end) as lk,
                  sum(case when t.tip_raboty between 2 and 10 or t.tip_raboty=12 then t.chasy else 0 end) as pr,
                  sum(case when t.tip_raboty=11 then t.chasy else 0 end) as srs
                from
                razdel_kursa as rk
                INNER JOIN podrazdel_kursa as pk on rk.id = pk.razdel
                LEFT JOIN tema as t on pk.id = t.podrazdel
                where rk.kurs = :kurs and rk.nazvanie=7
                group by pk.id
                ORDER BY pk.id
                ';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':kurs',$kurs_id)->queryAll();
        $is_error=false;
        foreach ($res as $k=>$v) {
            if ($res[0]['chasy']!=$v['chasy'] || $res[0]['lk']!=$v['lk'] || $res[0]['pr']!=$v['pr'] || $res[0]['srs']!=$v['srs']){
                $is_error = true;
                break;
            }
        }
        return $is_error;
    }

}
