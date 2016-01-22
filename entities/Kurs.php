<?php
namespace app\entities;

use app\transformers\DateTransformer;
use app\transformers\EnumArrayTransformer;
use app\enums\FormaObucheniya;
use app\enums\StatusZapisiNaKurs;
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
 * @property string $trebovaniyaKUrovnyuPodgotovki
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
                $q->onCondition(['{{kurs_fiz_lica}}.[[status]]' => StatusZapisiNaKurs::asSql(StatusZapisiNaKurs::ZAPIS)]);
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
            ->hasOne(FormaItogovojAttestaciiKursa::className(), ['id' => 'forma_itogovoj_attestacii']);
    }
}
