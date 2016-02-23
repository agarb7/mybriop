<?php
namespace app\modules\plan_prospekt\models;

use app\records\Kurs;

/**
 * KursDelete model for delet action
 * @property boolean $canBeDeleted
 */
class KursDelete extends Kurs
{
    private $_canBeDeleted;

    public static function tableName()
    {
        return "kurs";
    }

    public function getCanBeDeleted($recheck = false)
    {
        if ($this->_canBeDeleted === null || $recheck) {
            $this->_canBeDeleted = !$this->getFiz_lica_rel()->exists()
                && !$this->getRazdely_kursa_rel()->exists();
        }

        return $this->_canBeDeleted;
    }
}