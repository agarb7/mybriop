<?php
namespace app\models\kurs_slushatelyu;

use DateTime;

use app\enums2\StatusKursaFizLica;
use Yii;

use app\records\KursFizLica;

class InfoOIupForm extends KursFizLica
{
    /**
     * @param $kursId integer
     * @return boolean
     */
    public function iup($kursId)
    {
        $this->kurs = $kursId;
        $this->fiz_lico = Yii::$app->user->fizLicoId;
        $this->status = StatusKursaFizLica::OZHIDAET_PODTVERZHDENIYA;
        $this->iup = true;
        $this->vremya_smeny_statusa = (new DateTime)->format(DATE_ISO8601);

        $newRecord = !KursFizLica::find()
            ->where(['kurs' => $this->kurs, 'fiz_lico' => $this->fiz_lico])
            ->exists();

        $this->setIsNewRecord($newRecord);

        return $this->save();
    }
}