<?php
namespace app\rbac;

use app\entities\Polzovatel;
use app\enums\Rol;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\rbac\Assignment;
use yii\rbac\ManagerInterface;
use yii\rbac\Permission;
use yii\rbac\Role;

//todo rules not working: needs review concept
class StaticAuthManager extends Object implements ManagerInterface
{
    /**
     * @inheritdoc
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        $assignments = $this->getAssignments($userId);
        return $this->checkAccessRecursive($userId, $permissionName, $params, $assignments);
    }

    public function createRole($name)
    {
        throw new NotSupportedException('Создавать новые роли динамически нельзя.');
    }

    public function createPermission($name)
    {
        throw new NotSupportedException('Создавать новые права динамически нельзя.');
    }

    public function add($object)
    {
        throw new NotSupportedException('Динамически добавлять в менеджер новые роли, права или правила нельзя.');
    }

    public function remove($object)
    {
        throw new NotSupportedException('Динамически удалять из менеджера роли, права или правила нельзя.');
    }

    public function update($name, $object)
    {
        throw new NotSupportedException('Динамически обновлять в менеджере роли, права или правила нельзя.');
    }

    /**
     * @inheritdoc
     */
    public function getRole($name)
    {
        return $this->getItem($this->_rolesConfig, Role::className(), $name);
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->getItems($this->_rolesConfig, Role::className());
    }

    /**
     * @inheritdoc
     */
    public function getRolesByUser($userId)
    {
        /**
         * @var $polzovatel Polzovatel
         */
        $polzovatel = Polzovatel::findOne(['login' => $userId]);
        $roles = [];

        if ($polzovatel) {
            foreach ($polzovatel->roliAsArray as $role_name)
                $roles[$role_name] = $this->getRole($role_name);
        }

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function getPermission($name)
    {
        return $this->getItem($this->_permissionsConfig, Permission::className(), $name);
    }

    /**
     * @inheritdoc
     */
    public function getPermissions()
    {
        return $this->getItems($this->_permissionsConfig, Permission::className());
    }

    /**
     * @inheritdoc
     */
    public function getPermissionsByRole($roleName)
    {
        $marks = [];
        $permissions = [];

        $this->getPermissionsRecursive($roleName, $marks, $permissions);

        return $permissions;
    }

    /**
     * Всегда возвращает пустой массив
     * @param string|integer $userId
     * @return array
     */
    public function getPermissionsByUser($userId)
    {
        return [];
    }


    /**
     * @inheritdoc
     */
    public function getRule($name)
    {
        return null;

        //todo

//        if (!ArrayHelper::keyExists($name, $this->_rulesConfig))
//            return null;
//
//        $rule_config = $this->_rulesConfig[$name];
//
//        return $this->makeRule($name, $rule_config);
    }

    /**
     * @inheritdoc
     */
    public function getRules()
    {
        return [];

        //todo

//        $rules = [];
//
//        foreach ($this->_rulesConfig as $name => $config)
//            $rules[] = $this->makeRule($name, $config);
//
//        return $rules;
    }

    public function addChild($parent, $child)
    {
        throw new NotSupportedException('Динамически изменять иерархию ролей и прав нельзя.');
    }

    public function removeChild($parent, $child)
    {
        throw new NotSupportedException('Динамически изменять иерархию ролей и прав нельзя.');
    }

    public function removeChildren($parent)
    {
        throw new NotSupportedException('Динамически изменять иерархию ролей и прав нельзя.');
    }

    /**
     * @inheritdoc
     */
    public function hasChild($parent, $child)
    {
        $children = $this->_dag[$parent->name];
        return ArrayHelper::keyExists($child->name, $children);
    }

    /**
     * @inheritdoc
     */
    public function getChildren($name)
    {
        $children = $this->_dag[$name];

        $items = [];

        foreach ($children as $child => $ignored) {
            $items[$child] = $this->getRole($child);
            if (!$items[$child])
                $items[$child] = $this->getPermission($child);
        }

        return $items;
    }

    /**
     * @inheritdoc
     */
    public function assign($role, $userId)
    {
        $repo = new PolzovatelRepo($this->db);

        $polzovatel = $repo->selectOne(['login' => $userId]);
        if (!$polzovatel)
            return null;

        $role_name = $role->name;

        if ($polzovatel->hasRol($role_name))
            throw new InvalidParamException('Пользователь уже имеет эту роль.');

        $polzovatel->addRol($role_name);

        $repo->save($polzovatel);

        return new Assignment([
            'userId' => $userId,
            'roleName' => $role_name
        ]);
    }

    /**
     * @inheritdoc
     */
    public function revoke($role, $userId)
    {
        $repo = new PolzovatelRepo($this->db);

        $polzovatel = $repo->selectOne(['login' => $userId]);
        if (!$polzovatel)
            return false;

        $polzovatel->removeRol($role->name);

        return $repo->save($polzovatel);
    }

    public function revokeAll($userId)
    {
        throw new NotSupportedException('Нельзя отозвать все роли пользователя.');
    }

    /**
     * @inheritdoc
     */
    public function getAssignment($roleName, $userId)
    {
        $cond = [
            ['login' => $userId],
            Rol::asSql($roleName) . ' = any (roli)'
        ];

        if (!Polzovatel::findOne($cond))
            return null;

        return new Assignment([
            'userId' => $userId,
            'roleName' => $roleName
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        $assigments = [];

        $polzovatel = Polzovatel::findIdentity($userId);
        if ($polzovatel) {
            foreach ($polzovatel->roliAsArray as $roleName) {
                $assigments[$roleName] = new Assignment([
                    'userId' => $userId,
                    'roleName' => $roleName
                ]);
            }
        }

        return $assigments;
    }

    public function removeAll()
    {
        throw new NotSupportedException('Нельзя удалить все авторизационные данные.');
    }

    public function removeAllPermissions()
    {
        throw new NotSupportedException('Нельзя удалять права.');
    }

    public function removeAllRoles()
    {
        throw new NotSupportedException('Нельзя удалять роли.');
    }

    public function removeAllRules()
    {
        throw new NotSupportedException('Нельзя удалять правила.');
    }

    public function removeAllAssignments()
    {
        //todo реализовать после проработки репозитария для сущности пользователя
        throw new NotSupportedException('Пока не реализовано.');
    }

//    /**
//     * @param array $config Ключ элемента массива - это имя роли, значение - конфигурация правила. Если ключ не строковый,
//     *  то значение - это имя роли, а правило считается незаданным.
//     *  Конфигурация правила - это либо имя класса, либо callable для свойства executor объекта LambdaRule.
//     *  Например:
//     *  [
//     *      'admin',
//     *      'rektor' => RektorRule,
//     *      'rukovoditel' => function ($user, $item, $params) {
//     *          return UserHelper::isRukovoditel($user);
//     *      }
//     *  ]
//     */
    public function setRolesConfig($config)
    {
        self::assignItemsConfig($this->_rolesConfig, $config);
    }

//    /**
//     * @param array $config Ключ элемента массива - это имя права, значение - конфигурация правила. Если ключ не строковый,
//     *  то значение - это имя роли, а правило считается незаданным.
//     *  Конфигурация правила - это либо имя класса, либо callable для свойства executor объекта LambdaRule.
//     *  Например:
//     *  [
//     *      'zapisatsya',
//     *      'redaktirovat-profil' => ProfilRule,
//     *      'otmenit-zapis' => function ($user, $item, $params) {
//     *          return hasZapis($user, $params['zapis_id']);
//     *      }
//     *  ]
//     */
    public function setPermissionsConfig($config)
    {
        self::assignItemsConfig($this->_permissionsConfig, $config);
    }

    // just stub
    /**
     * @inheritdoc
     */
    public function getUserIdsByRole($roleName)
    {
        return [];
    }

    /**
     * @param array $config Список смежности для DAG (directed acyclic graph, направленный ациклический граф).
     *   Ключ элемента - имя родителя, значение - массив именён детей, или строка с именем единственного ребёнка.
     */
    public function setDag($config)
    {
        $this->_dag = [];

        foreach ($config as $parent => $children) {
            if (is_array($children))
                $this->_dag[$parent] = array_flip($children);
            else
                $this->_dag[$parent] = [$children => null];
        }
    }

//    /**
//     * Присваивает конфигурацию элементов авторизации, преобразуя во внутренний формат хранения.
//     * @param array $left Присваивается преобразованное значение $right.
//     * @param array $right Ключ элемента массива - это имя права, значение - конфигурация правила. Если ключ не строковый,
//     *  то значение - это имя роли, а правило считается незаданным.
//     *  Конфигурация правила - это либо имя класса, либо callable для свойства executor объекта LambdaRole.
//     */
    private function assignItemsConfig(&$left, $right)
    {
        $left = [];

        foreach ($right as $key => $value) {
            if (is_string($key))
                $left[$key] = ArrayHelper::merge($value, ['name' => $key]);
            else
                $left[$value] = ['name' => $value];
        }
    }

    private function getItem($config, $class, $name)
    {
        if (!ArrayHelper::keyExists($name, $config))
            return null;

        return new $class($config[$name]);
    }

    private function getItems($config, $class)
    {
        return array_map(function ($item_config) use ($class) {
            return new $class($item_config);
        }, $config);
    }

    private function getPermissionsRecursive($itemName, &$marks, &$permissions)
    {
        if (ArrayHelper::keyExists($itemName, $marks))
            return;

        if ($perm = $this->getPermission($itemName))
            $permissions[$itemName] = $perm;

        $marks[$itemName] = null;

        foreach ($this->_dag[$itemName] as $child=>$ignored)
            $this->getPermissionsRecursive($itemName, $marks, $permissions);
    }

    private function checkAccessRecursive($user, $itemName, $params, $assignments)
    {
        $item = $this->getRole($itemName);
        if (!$item)
            $item = $this->getPermission($itemName);

        if (!$item)
            throw new InvalidConfigException('Элемент авторизации (роль или право) не найден.');

        if (!$this->executeRule($user, $item, $params))
            return false;

        if (isset($assignments[$itemName]))
            return true;

        foreach ($this->_dag as $parentName => $children) {
            if (ArrayHelper::keyExists($itemName, $children)
                && $this->checkAccessRecursive($user, $parentName, $params, $assignments))
            {
                return true;
            }
        }

        return false;
    }

    private function executeRule($user, $item, $params)
    {
        if ($item->ruleName === null)
            return true;

        $rule = $this->getRule($item->ruleName);
        if (!$rule)
            throw new InvalidConfigException('Правило не найдено');

        return $rule->execute($user, $item, $params);
    }

//    private function addRuleConfig($rule_config, $item_name)
//    {
//        $rule_name = is_callable($rule_config) //callable or class name
//            ? $this->makeRuleName($item_name)
//            : (new $rule_config)->name;
//
//        $this->_rulesConfig[$rule_name] = $rule_config;
//        return $rule_name;
//    }

//    private function makeRuleName($item_name)
//    {
//        return "{$item_name}-rule";
//    }

//    private function makeRule($name, $config)
//    {
//        if (is_callable($config)) {
//            return new LambdaRule([
//                'name' => $name,
//                'executor' => $config
//            ]);
//        }
//
//        return new $config; // class name
//    }

    public $_rolesConfig;
    public $_permissionsConfig;

    private $_dag;

    //todo rewrite this and review concept
//    private $_rulesConfig;
}