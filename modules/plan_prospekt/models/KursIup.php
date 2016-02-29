<?php
namespace app\modules\plan_prospekt\models;

use app\records\Kurs;

/**
 * Class KursIup for iup action
 * @package app\modules\plan_prospekt\models
 */
class KursIup extends Kurs
{
    public function rules()
    {
        return [
            ['iup', 'boolean'],
            ['iup', 'default', 'value' => false]
        ];
    }
}