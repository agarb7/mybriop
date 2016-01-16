<?php
namespace app\behaviors;

use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use Yii;
use yii\helpers\VarDumper;

class DirectoryBehavior extends Behavior
{
    /**
     * @var array In form ['<relation>' => '<attribute>']
     */
    public $directoryAttributes = [];

    private $_dirtyDirectories = [];
    private $_directoryRelations;

    public function canGetProperty($name, $checkVars = true)
    {
        $rels = array_flip($this->directoryAttributes);

        return isset($rels[$name]) || parent::canGetProperty($name, $checkVars);
    }

    public function canSetProperty($name, $checkVars = true)
    {
        $rels = array_flip($this->directoryAttributes);

        return isset($rels[$name]) || parent::canSetProperty($name, $checkVars);
    }

    public function __get($name)
    {
        $rels = array_flip($this->directoryAttributes);

        if (isset($rels[$name]))
            return $this->getDirectory($rels[$name]);

        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        $rels = array_flip($this->directoryAttributes);

        if (isset($rels[$name]))
            $this->setDirectory($rels[$name], $value);
        else
            parent::__set($name, $value);
    }

    public function __isset($name)
    {
        $rels = array_flip($this->directoryAttributes);

        if (isset($rels[$name]))
            return true;

        return parent::__isset($name);
    }

    /**
     * @return array List of relation's name that consider as directories
     */
    public function getDirectoryRelations()
    {
        if ($this->_directoryRelations !== null)
            return $this->_directoryRelations;

        return array_keys($this->directoryAttributes);
    }

    /**
     * @param array $relations List of relation's name that consider as directories
     */
    public function setDirectoryRelations($relations)
    {
        $this->_directoryRelations = $relations;
    }

    /**
     * @param string $relation
     * @return array|null null or array in form ['id' => <id>] or ['nazvanie' => '<nazvanie>'];
     */
    public function getDirectory($relation)
    {
        if (!in_array($relation, $this->directoryRelations))
            throw new InvalidParamException("'$relation' is not directory relation");

        if (array_key_exists($relation, $this->_dirtyDirectories))
            return $this->_dirtyDirectories[$relation];

        /* @var $owner ActiveRecord */
        $owner = $this->owner;

        $row = $owner->getRelation($relation)
            ->select(['id', 'nazvanie', 'obschij'])
            ->asArray()
            ->one();

        if (!$row)
            return null;

        return $row['obschij']
            ? ['id' => $row['id']]
            : ['nazvanie' => $row['nazvanie']];
    }

    /**
     * @param string $relation
     * @param array|null $directory Array must be in form ['id' => <id>] or ['nazvanie' => '<nazvanie>'];
     */
    public function setDirectory($relation, $directory)
    {
        if (!in_array($relation, $this->directoryRelations))
            throw new InvalidParamException("'$relation' is not directory relation");

        $this->_dirtyDirectories[$relation] = $directory;
    }

    public function withDirectoriesSafeDelete()
    {
        return Yii::$app->db->transaction(function () {
            return $this->withDirectoriesDelete();
        });
    }

    public function withDirectoriesSafeSave()
    {
        return Yii::$app->db->transaction(function () {
            return $this->withDirectoriesSave();
        });
    }

    /**
     * Delete entity and, if directories are not records listed in $relations. see [[linkDirectories]]
     * @throws \Exception
     */
    public function withDirectoriesDelete()
    {
        /* @var $owner ActiveRecord */
        $owner = $this->owner;

        $dirs = [];
        foreach ($this->getDirectoryRelations() as $rel)
            $dirs[$rel] = ['id' => $owner->{$this->getLinkColumn($rel)}];

        if ($owner->delete() === false)
            return false;

        foreach ($dirs as $rel => $dir) {
            if (!$this->deleteDirectory($rel, $dir))
                return false;
        }

        return true;
    }

    /**
     * Save and delete linked directories records if they is not common.
     * @return bool
     */
    public function withDirectoriesSave()
    {
        /* @var $owner ActiveRecord */
        $owner = $this->owner;

        if (!$owner->validate())
            return false;

        $dirsToInsert = [];
        $dirsToDelete = [];
        $dirIds = [];

        foreach ($this->_dirtyDirectories as $rel => $dir) {
            $this->destroyStoredDirectory($rel, $dirsToDelete);
            $this->initNewDirectory($rel, $dir, $dirsToInsert, $dirIds);
        }

        foreach ($dirsToInsert as $rel => $dir) {
            if (!$this->insertDirectory($rel, $dir, $dirIds))
                return false;
        }

        foreach ($dirIds as $rel => $id)
            $owner->{$this->getLinkColumn($rel)} = $id;

        if (!$owner->save(false))
            return false;

        foreach ($dirsToDelete as $rel => $dir) {
            if (!$this->deleteDirectory($rel, $dir))
                return false;
        }

        return true;
    }

    private function destroyStoredDirectory($relation, &$toDelete)
    {
        /* @var $owner ActiveRecord */
        $owner = $this->owner;

        $row = $owner->getRelation($relation)
            ->select(['id', 'obschij'])
            ->asArray()
            ->one();

        if (!$row['obschij'])
            $toDelete[$relation] = ['id' => $row['id']];
    }

    private function initNewDirectory($relation, $directory, &$toInsert, &$ids)
    {
        if ($directory === null)
            $ids[$relation] = null;
        elseif (isset($directory['id']))
            $ids[$relation] = $directory['id'];
        else
            $toInsert[$relation] = $directory;
    }

    private function insertDirectory($relation, $directory, &$ids)
    {
        /* @var $record ActiveRecord */

        $directory['class'] = $this->getModelClass($relation);
        $directory['obschij'] = false;
        $record = Yii::createObject($directory);

        if (!$record->save(false))
            return false;

        $ids[$relation] = $record->id;
        return true;
    }

    private function deleteDirectory($relation, $directory)
    {
        /* @var $record ActiveRecord */

        $directory['class'] = $this->getModelClass($relation);
        $record = Yii::createObject($directory);

        if ($record->delete() === false)
            return false;

        return true;
    }

    private function getModelClass($rel)
    {
        /* @var $owner ActiveRecord */
        $owner = $this->owner;
        return $owner->getRelation($rel)->modelClass;
    }

    private function getLinkColumn($rel)
    {
        /* @var $owner ActiveRecord */
        $owner = $this->owner;
        return $owner->getRelation($rel)->link['id'];
    }
}