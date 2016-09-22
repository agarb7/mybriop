<?php
namespace app\base;

use yii\db\Connection;
use yii\helpers\ArrayHelper;

//todo not for all but for directories
class ActiveQuery extends \yii\db\ActiveQuery
{    
    /**
     * @param string|\Closure $column
     * @param string|\Closure $keyColumn
     * @param bool $onlyObschij
     * @param Connection|null $db
     * @return array
     */
    public function listItems($column = 'nazvanie', $keyColumn = 'id', $onlyObschij = true, $db = null)
    {
        $query = clone $this;
        
        $query->asArray();

        if ($onlyObschij && $this->getHasObschij())
            $query->andWhere(['obschij' => true]);

        if (is_string($column) && is_string($keyColumn) && !$this->select)
            $query->select([$keyColumn, $column]);
        
        return ArrayHelper::map($query->all($db), $keyColumn, $column);
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