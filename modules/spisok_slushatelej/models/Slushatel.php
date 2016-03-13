<?php
namespace app\modules\spisok_slushatelej\models;

use app\records\FizLico;
use yii\db\ActiveQuery;

class Slushatel extends FizLico
{
    public $kurs;
    public $status;
    public $iup;

    /**
     * @param $kurs
     * @return ActiveQuery
     */
    public static function findForKurs($kurs)
    {
        return parent::find()
            ->select([
                'fiz_lico.*',
                'kurs' => 'kurs_fiz_lica.kurs',
                'status' => 'kurs_fiz_lica.status',
                'iup' => 'kurs_fiz_lica.iup'
            ])
            ->with('raboty_fiz_lica_rel')
            ->joinWith('kursy_fiz_lica_rel', false)
            ->where(['kurs_fiz_lica.kurs' => $kurs]);
    }
}