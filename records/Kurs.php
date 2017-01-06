<?php
namespace app\records;

use app\base\ActiveQuery;
use app\base\ActiveRecord;

/**
 * Kurs record
 * @property int $id
 * @property string $nazvanie
 * @property string $annotaciya
 * @property string $tip
 * @property int $prisvaemaya_kvalifikaciya
 * @property string $aktualnost
 * @property string $cel
 * @property string $zadachi
 * @property string $planiruemye_rezultaty
 * @property string $harakteristika_novoj_kvalifikacii
 * @property string $trebovaniya_k_urovnyu_podgotovki
 * @property string $forma_obucheniya
 * @property string $informacionnye_usloviya
 * @property string $kadrovye_usloviya
 * @property string $uchebnometodicheskie_usloviya
 * @property string $tehnicheskie_usloviya
 * @property string $itogovaya_attestaciya
 * @property string $rezhim_zanyatij
 * @property string $spisok_literatury
 * @property int $forma_itogovoj_attestacii
 * @property int $chasy_itogovoj_attestacii
 * @property string $opisanie_itogovoj_attestacii
 * @property int $nedelya_itogovoj_attestacii
 * @property string $finansirovanie
 * @property int $strukturnoe_podrazdelenie
 * @property int $rukovoditel
 * @property string $raschitano_chasov
 * @property int $raschitano_slushatelej
 * @property int $maksimalno_slushatelej
 * @property string $formy_obucheniya
 * @property string $ochnoe_nachalo
 * @property string $ochnoe_konec
 * @property string $zaochnoe_nachalo
 * @property string $zaochnoe_konec
 * @property string $status_programmy
 * @property string $status_raspisaniya
 * @property string $harakteristika_novogo_vida_deyatelnosti
 * @property string $sostaviteli
 * @property string $recenzenti
 * @property string $itogovaya_attestaciya_tekst
 * @property string $plan_prospekt_god
 * @property boolean $iup
 * @property string $raspisanie_nachalo
 * @property string $raspisanie_konec
 * @property integer $auditoriya_po_umolchaniyu
 * @property KategoriyaSlushatelya[] $kategorii_slushatelej_rel
 */
class Kurs extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "kurs";
    }

    /**
     * @return ActiveQuery
     */
    public function getKategorii_slushatelej_rel()
    {
        return $this
            ->hasMany(KategoriyaSlushatelya::className(), ['id' => 'kategoriya_slushatelya'])
            ->viaTable('kategoriya_slushatelya_kursa', ['kurs' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRukovoditel_rel()
    {
        return $this->hasOne(FizLico::className(), ['id' => 'rukovoditel']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRazdely_kursa_rel()
    {
        return $this
            ->hasMany(RazdelKursa::className(), ['kurs' => 'id'])
            ->inverseOf('kurs_rel');
    }

    /**
     * @return ActiveQuery
     */
    public function getFiz_lica_rel()
    {
        return $this
            ->hasMany(FizLico::className(), ['id' => 'fiz_lico'])
            ->viaTable('kurs_fiz_lica', ['kurs' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAuditoriya_po_umolchaniyu_rel()
    {
        return $this->hasOne(Auditoriya::className(), ['id' => 'auditoriya_po_umolchaniyu']);
    }

}