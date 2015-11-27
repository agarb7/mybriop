<?php
namespace app\helpers;

use app\entities\EntityBase;
use yii\base\InvalidParamException;

class DirectoryHelper
{
    /**
     * @param $class
     * @param $newId
     * @param $newValue
     * @param $curId
     * @param null $newAdditionalValues
     * @param string $valueAttribute
     * @return EntityBase[]|array [$curEntity, $entityToDelete]
     */
    public static function getFromCombo($class, $newId, $newValue, $curId, $newAdditionalValues = null, $valueAttribute = 'nazvanie')
    {
        if ($newId !== null && $newValue !== null)
            throw new InvalidParamException("Both new id and new value is not null. Id is $newId, value is $newValue.");

        if ($newId == null && $newValue === null) {
            $toDelete = $curId !== null
                ? self::nullIfCommon($class, $curId)
                : null;

            return [null, $toDelete];
        }

        if ($newId === null && $newValue !== null) {
            if ($curId !== null && ($found = $class::findOne($curId)) && !$found->obschij)
                $entity = $found;
            else
                $entity = new $class(['obschij' => false]);

            self::setEntityValues($entity, $valueAttribute, $newValue, $newAdditionalValues);

            return [$entity, null];
        }

        if ($newId !== null && $newValue == null) {
            $entity = $class::findOne($newId);

            if ($curId === null || $newId === $curId)
                $toDelete = null;
            else
                $toDelete = self::nullIfCommon($class, $curId);

            return [$entity, $toDelete];
        }

        throw new InvalidParamException;
    }

    public static function getForCombo($entity, $valueAttribute = 'nazvanie')
    {
        if (!$entity)
            return [null, null];

        return $entity->obschij
            ? [$entity->id, null]
            : [null, $entity->$valueAttribute];
    }

//    /**
//     * @param Entity $curEntity
//     * @param Entity $entityToDelete
//     */
//    public static function persistCombo($curEntity, $entityToDelete)
//    {
//        if ($curEntity)
//            $curEntity->save(false);
//        if ($entityToDelete)
//            $entityToDelete->delete();
//    }

    private static function nullIfCommon($class, $id)
    {
        $entity = $class::find()
            ->where(['id' => $id])
            ->select(['id','obschij'])
            ->one();

        if(!$entity || $entity->obschij)
            return null;

        return $entity;
    }

    private static function setEntityValues($entity, $attribute, $value, $additional)
    {
        $entity->$attribute = $value;
        foreach ($additional ?: [] as $attr => $val)
            $entity->$attr = $val;
    }
}