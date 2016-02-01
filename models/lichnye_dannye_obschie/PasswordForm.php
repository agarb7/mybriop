<?php
namespace app\models\lichnye_dannye_obschie;

use app\entities\Polzovatel;

class PasswordForm extends Polzovatel
{
    public $password_repeat;

    public function getPassword()
    {
        return $this->_password;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
        $this->setParol($password);
    }

    public static function tableName()
    {
        return 'polzovatel';
    }

    public function rules()
    {
        return [
            [['password','password_repeat'], 'required'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password']
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля'
        ];
    }

    private $_password;
}