<?php
namespace app\modules\spisok_slushatelej\models;

use app\records\KursFizLica;

class KursSlushatelya extends KursFizLica
{
    public function setStatusAndVremya($status)
    {
        $this->status = $status;
        $this->vremya_smeny_statusa = date(DATE_ISO8601);
    }
}