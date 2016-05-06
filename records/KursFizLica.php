<?php
namespace app\records;

use app\base\ActiveRecord;

/**
 * KursFizLica record
 * @property int id
 * @property int fiz_lico
 * @property int kurs
 * @property int dolzhnost_fiz_lica_na_rabote
 * @property string dokument_ob_obrazovanii_seriya
 * @property string dokument_ob_obrazovanii_nomer
 * @property string dokument_ob_obrazovanii_data
 * @property int dokument_ob_obrazovanii_kopiya
 * @property string status
 * @property boolean iup
 * @property string vremya_smeny_statusa
 */
class KursFizLica extends ActiveRecord
{
    public static function tableName()
    {
        return 'kurs_fiz_lica';
    }

    public function getFiz_lico_rel()
    {
        return $this
            ->hasOne(FizLico::className(), ['id' => 'fiz_lico'])
            ->inverseOf('kursy_fiz_lica_rel');
    }
}