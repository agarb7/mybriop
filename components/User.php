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
}