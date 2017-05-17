<?php
use app\components\Formatter;
use app\modules\spisok_slushatelej\Asset;
use app\modules\spisok_slushatelej\grid\FioColumn;
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

$flash = \Yii::$app->session->getAllFlashes();
if ($flash){
    $js = '';
    foreach ($flash as $k => $v) {
        $js .= 'bsalert("'.$v.'","'.$k.'","top");'."\n";
    }
    $this->registerJS('$(function(){'.$js.'})');
}
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
                'class' => FioColumn::className(),
                'header' => 'Ф. И. О.',
                'headerOptions' => ['class' => 'cell-data'],
                'contentOptions' => ['class' => 'cell-data']
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