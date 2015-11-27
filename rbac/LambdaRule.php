<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.06.15
 * Time: 16:40
 */

namespace app\rbac;

//todo review concept
class LambdaRule extends \yii\rbac\Rule
{
    /**
     * @inheritdoc
     */
    public function execute($user, $item, $params)
    {
        return  call_user_func($this->executor, $user, $item, $params);
    }

    /**
     * @var callable
     */
    public $executor;
}
