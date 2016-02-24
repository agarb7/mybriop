<?php
use yii\helpers\Html;

echo Html::tag('div', null, ['class' => 'hidden data', 'data' => [
    'tohide' => isset($model) ? 0 : 1,
    'backurl' => isset($backUrl) ? $backUrl : null
]]);
