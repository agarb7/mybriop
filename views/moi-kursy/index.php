<?php
use yii\helpers\Html;

$this->title = 'Список курсов';

echo Html::ul($kursy, ['item' => function($item, $index) {
    return Html::tag(
        'li',
        Html::a($item['nazvanie'],'/moi-kursy/list?kurs='.$item['id']),
        ['class' => 'list-group']
    );
}]);