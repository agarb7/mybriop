<?php
namespace app\upravlenie_kursami\raspisanie\models;

use yii\base\InvalidParamException;
use yii\base\Model;

class Day extends Model
{
    /**
     * Maximum zanyatij per day
     *
     * @var int
     */
    public static $zanyatiyaMax = 6;

    public $data;

    private $_zanyatiya = [];
    private $_zanyatiyaSorted = true;

    /**
     * @return Zanyatie[]
     */
    public function getZanyatiya()
    {
        if (!$this->_zanyatiyaSorted) {
            ksort($this->_zanyatiya);
            $this->_zanyatiyaSorted = true;
        }

        return $this->_zanyatiya;
    }

    /**
     * @param Zanyatie $zanyatie
     * @throws InvalidParamException
     */
    public function addZanyatie($zanyatie)
    {
        $this->_zanyatiya[$zanyatie->nomer] = $zanyatie;
        $this->_zanyatiyaSorted = false;
    }
}