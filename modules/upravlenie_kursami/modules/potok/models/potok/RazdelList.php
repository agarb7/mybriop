<?php
namespace app\upravlenie_kursami\potok\models\potok;

use yii\base\Object;

class RazdelList extends Object
{
    private $_list = [];

    private $_lastRazdelRef;
    private $_lastPodrazdelRef;

    public function toArray()
    {
        return $this->_list;
    }

    public function getLastRazdel()
    {
        return $this->_lastRazdelRef;
    }

    public function getLastPodrazdel()
    {
        return $this->_lastPodrazdelRef;
    }

    public function addRazdel($razdel)
    {
        $this->_list[] = $razdel;

        $this->_lastRazdelRef = &$this->lastItemRef($this->_list);
    }

    public function addPodrazdel($podrazdel)
    {
        $this->_lastRazdelRef['podrazdely'][] = $podrazdel;

        $this->_lastPodrazdelRef = &$this->lastItemRef($this->_lastRazdelRef['podrazdely']);
    }

    public function addChastTemy($tema)
    {
        $this->_lastPodrazdelRef['temy'][] = $tema;
    }

    private function &lastItemRef(&$array)
    {
        return $array[count($array)-1];
    }
}