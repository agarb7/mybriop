<?php
namespace app\modules\plan_prospekt\grid;

use yii\helpers\Html;

class DataColumn extends \yii\grid\DataColumn
{
    protected function renderHeaderCellContent()
    {
        $parentHeader = parent::renderHeaderCellContent();

        $sort = $this->grid->dataProvider->getSort();
        $direction = $sort->getAttributeOrder($this->attribute);

        if ($direction === null)
            return $parentHeader;

        $class = $direction == SORT_ASC ? 'asc' : 'desc';
        return $parentHeader . Html::tag('span', '', ['class' => [$class]]);
    }
}