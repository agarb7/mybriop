<?php
namespace app\rbac;

use yii\base\Action;
use yii\base\ActionFilter;
use yii\di\Instance;
use yii\web\ForbiddenHttpException;
use yii\web\User;
use Yii;

class RbacAccessControl extends ActionFilter
{
    public function init()
    {
        parent::init();
        $this->user = Instance::ensure($this->user, User::className());
    }

    /**
     * @param Action $action the action to be executed.
     * @return boolean whether the action should continue to be executed.
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        $user = $this->user;
        $get = Yii::$app->request->get();
        foreach ($this->rules as $action_id => $auth_item) {
            if ($action_id != '*' && $action_id != $action->id)
                continue;

            if ($auth_item === '*') {
                return true;
            } elseif ($auth_item === '?') {
                if ($user->isGuest)
                    return true;
            } elseif ($auth_item === '@') {
                if (!$user->isGuest)
                    return true;
            } elseif ($user->can($auth_item, $get)) {
                return true;
            }
        }

        $this->denyAccess($user);
        return false;
    }

    /**
     * @param User $user the current user
     * @throws ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }

    /**
     * @var User|array|string the user object representing the authentication status or the ID of the user application component.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $user = 'user';

    /**
     * @var array $rules Ключ - action ID или * (совпадает с любым action ID), значение - имя правила или роли.
     */
    public $rules = [];
}