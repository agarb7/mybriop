<?php
namespace app\components;

use app\rbac\RbacAccessControl;

class Controller extends \yii\web\Controller
{
    public function init()
    {
        parent::init();

        $this->attachBehavior('accessControl',[
            'class' => RbacAccessControl::className(),
            'rules' => $this->accessRules()
        ]);
    }

    /**
     * Override in descendant classes and determine who can access to certain actions.
     * Returns array, each element in it must be:
     * ['action-id, *' => 'role, permission, @, ?, *']
     *
     * @return array Rules that determine who can access to certain actions.
     */
    public function accessRules()
    {
        return [];
    }
}