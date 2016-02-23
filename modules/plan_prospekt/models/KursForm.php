<?php
namespace app\modules\plan_prospekt\models;

use app\records\Kurs;

class KursForm extends Kurs
{
    public static function tableName()
    {
        return "kurs";
    }

    public function rules()
    {
        return [
            ['nazvanie', 'required'],

            ['plan_prospekt_god', 'required'],
            ['plan_prospekt_god', 'in', 'range' => ['2015-01-01', '2016-01-01']],

            ['iup', 'boolean']
        ];
    }
}