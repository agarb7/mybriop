<?php

namespace app\models\polzovatel;

use app\behaviors\PolzovatelProperty;
use app\entities\Polzovatel;
use app\validators\LoginValidator;
use yii\base\Model;

/**
 * Class PodtverzhdenieEmail
 * @package app\models\polzovatel
 *
 * @property Polzovatel $polzovatel
 */
class PodtverzhdenieEmail extends Model
{
    public $login;
    public $kod;

    public function rules()
    {
        return [
            ['login', 'required'],
            ['login', LoginValidator::className()],
            ['login', 'exist', 'targetAttribute' => 'login', 'targetClass' => Polzovatel::className()],

            ['kod', 'required'],
            ['kod', 'validateKod']
        ];
    }

    public function behaviors()
    {
        return [PolzovatelProperty::className()];
    }

    public function validateKod()
    {
        if (!$this->polzovatel || $this->polzovatel->kodPodtverzhdeniyaEmail !== $this->kod)
            $this->addError('kod', 'Неверный код.');
    }

    public function activatePolzovatel()
    {
        if (!$this->validate())
            return false;

        $this->polzovatel->aktiven = true;
        $this->polzovatel->kodPodtverzhdeniyaEmail = null;
        $this->polzovatel->save();

        return true;
    }
}
