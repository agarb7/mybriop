<?php
namespace app\modules\plan_prospekt\models;

use app\records\Kurs;
use Yii;

/**
 * KursDelete model for delet action
 * @property boolean $canBeDeleted
 */
class KursDelete extends Kurs
{
    private $_canBeDeleted;

    public function getCanBeDeleted($recheck = false)
    {
        if ($this->_canBeDeleted === null || $recheck) {
            $this->_canBeDeleted = !$this->getFiz_lica_rel()->exists()
                && !$this->getRazdely_kursa_rel()->exists();
        }

        return $this->_canBeDeleted;
    }

    public function delete()
    {
        return Yii::$app->db->transaction(function () {
            return $this->deleteImpl();
        });
    }

    private function deleteImpl()
    {
        $this->unlinkAll('kategorii_slushatelej_rel', true);

        return parent::delete();
    }
}