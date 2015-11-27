<?php

namespace app\behaviors;

use yii\base\Behavior;

class ParolBehavior extends Behavior
{
    public $solProperty = 'solParolya';
    public $heshProperty = 'heshParolya';

    public function setParol($parol)
    {
        $security = \Yii::$app->security;

        $solProperty = $this->solProperty;
        $heshProperty = $this->heshProperty;

        $this->owner->$solProperty = $security->generateRandomString(32);

        $solyonyj_parol = $parol . $this->owner->$solProperty;
        $this->owner->$heshProperty = $security->generatePasswordHash($solyonyj_parol, 10);
    }

    public function validateParol($parol)
    {
        $securety = \Yii::$app->security;

        $solProperty = $this->solProperty;
        $heshProperty = $this->heshProperty;

        $solyonyj_parol = $parol . $this->owner->$solProperty;

        return $securety->validatePassword($solyonyj_parol, $this->owner->$heshProperty);
    }
}
