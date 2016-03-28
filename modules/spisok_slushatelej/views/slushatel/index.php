<?php
use app\components\Formatter;
use app\modules\spisok_slushatelej\Asset;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\grid\SerialColumn;

use app\records\Kurs;

use app\modules\spisok_slushatelej\grid\ActionColumn;
use app\modules\spisok_slushatelej\grid\ContactsColumn;
use app\modules\spisok_slushatelej\grid\WorkColumn;
use yii\web\View;

/**
 * @var $provider ActiveDataProvider
 * @var $kursRecord Kurs
 * @var $this View
 */
Asset::register($this);
?>
<div class="spisokslushatelej-editor">

    <?= GridView::widget([
        'caption' =>  "Слушатели курса «{$kursRecord->nazvanie}»",
        'dataProvider' => $provider,
        'layout' => "{items}",
        'tableOptions' => ['class' => 'table'],
        'columns' => [
            [
                'class' => SerialColumn::className()
            ],
            [
                'header' => 'Ф. И. О.',
                'attribute' => 'fio',
                'headerOptions' => ['class' => 'cell-data'],
                'contentOptions' => ['class' => 'cell-data'],
                'value' => function ($fizLico) {
                    return Yii::$app->formatter->asFizLico($fizLico, Formatter::FIZ_LICO_FORMAT_FULL);
                }
            ],
            [
                'class' => ContactsColumn::className(),
                'header' => 'Личные контакты',
                'headerOptions' => ['class' => 'cell-data'],
                'contentOptions' => ['class' => 'cell-data']
            ],
            [
                'class' => WorkColumn::className(),
                'header' => 'Работа',
                'headerOptions' => ['class' => 'cell-data'],
                'contentOptions' => ['class' => 'cell-data']
            ],
            [
                'class' => ActionColumn::className(),
                'headerOptions' => ['class' => 'cell-action'],
                'contentOptions' => ['class' => 'cell-action']
            ]
        ]
    ]) ?>

</div>