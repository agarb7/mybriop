<?php
use yii\helpers\Html;

$this->title = 'Список дисциплин';

foreach ($spisok_discipline as $k=>$v) {
    echo '<h4>'.$k.'</h4>';
    echo Html::ul($v, ['item' => function($item, $index) {
        return Html::tag(
            'li',
            Html::a($item,'/kurs/rpd?id='.$index),
            ['class' => '']
        );
    }]);
}
