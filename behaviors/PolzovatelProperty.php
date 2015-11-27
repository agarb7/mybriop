<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.07.15
 * Time: 15:32
 */

namespace app\behaviors;


use app\entities\Polzovatel;
use yii\base\Behavior;

class PolzovatelProperty extends Behavior
{
    public $loginProperty = 'login';

    public function getPolzovatel()
    {
        $loginProp = $this->loginProperty;
        $login = $this->owner->$loginProp;

        if ($this->_oldLogin !== $login) {
            $this->_oldLogin = $login;
            $this->_polzovatel = Polzovatel::findIdentity($login);
        }
        
        return $this->_polzovatel;
    }

    /**
     * @var Polzovatel
     */
    private $_polzovatel;
    private $_oldLogin;
}