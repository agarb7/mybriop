<?php
namespace app\components;

class User extends \yii\web\User
{
    public function getFizLico($autoRenew = true)
    {
        $identity = $this->getIdentity($autoRenew);
        if ($identity && $identity->fizLico)
            return $identity->fizLicoRel;

        return null;
    }

    public function getFizLicoId($autoRenew = true)
    {
        $fizLico = $this->getFizLico($autoRenew);
        if ($fizLico !== null)
            return $fizLico->id;

        return null;
    }
}