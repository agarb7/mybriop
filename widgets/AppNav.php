<?php
namespace app\widgets;

use Yii;
use yii\bootstrap\Nav;

class AppNav extends Nav
{
    public function run()
    {
        $userId = Yii::$app->user->id;
        $roles = $userId ? Yii::$app->authManager->getRolesByUser($userId) : [];

        $itemsArray = array_map(function ($role) {
            return $role->data['menuItems'];
        }, $roles);

        array_unshift($itemsArray, $this->commonItems());

        $this->items = count($itemsArray) != 1
            ? call_user_func_array('app\helpers\ArrayHelper::merge', $itemsArray)
            : reset($itemsArray);

        return parent::run();
    }

    private function commonItems()
    {
        return [
        ];
    }
}
