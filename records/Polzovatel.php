<?php
namespace app\records;

use yii\db\ActiveRecord;

/**
 * Polzovatel record
 *
 * @property int $id
 * @property int $fiz_lico
 * @property string $klyuch_autentifikacii
 * @property string $hesh_parolya
 * @property string $sol_parolya
 * @property string $kod_podtverzhdeniya_email
 * @property boolean $aktiven
 * @property string $roli
 */
class Polzovatel extends ActiveRecord
{
    public function getFiz_lico_rel()
    {
        return $this
            ->hasOne(FizLico::className(), ['id' => 'fiz_lico'])
            ->inverseOf('polzovatel_rel');
    }
}