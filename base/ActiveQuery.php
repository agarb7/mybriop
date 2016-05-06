<?php
namespace app\base;

use yii\db\Connection;
use yii\helpers\ArrayHelper;

class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @param string|\Closure $column
     * @param bool $onlyObschij
     * @param Connection|null $db
     * @return array
     */
    public function listItems($column = 'nazvanie', $onlyObschij = true, $db = null)
    {
        $query = clone $this;
        
        $query->asArray();

        if ($onlyObschij && $this->getHasObschij())
            $query->andWhere(['obschij' => true]);

        if (is_string($column)) 
            $query->select(['id', $column]);
        
        return ArrayHelper::map($query->all($db), 'id', $column);
    }
    
    private function getHasObschij()
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        
        return ArrayHelper::keyExists(
            'obschij',
            $modelClass::getTableSchema()->columns
        );
    }
}