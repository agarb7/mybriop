<?php
namespace app\upravlenie_kursami\models;

use app\enums2\StatusProgrammyKursa;
use app\enums2\StatusRaspisaniyaKursa;

class Kurs extends \app\records\Kurs
{
    public function allowsZanyatiyaChange()
    {
        return $this->status_programmy === StatusProgrammyKursa::ZAVERSHENA
            && $this->status_raspisaniya === StatusRaspisaniyaKursa::REDAKTIRUETSYA;
    }
}