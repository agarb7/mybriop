<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;

echo GridView::widget([
    'dataProvider' => new ActiveDataProvider([
        'query' => Yii::$app->user->fizLico->getRabotyFizLicaRel()
    ])
]);