<?php
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var $data ActiveDataProvider
 */
?>
<?= GridView::widget([
    'dataProvider' => $data,
    'layout' => '{items}',
    'columns' => [
        'organizaciyaRel.nazvanie:text:Образовательное учреждение',
        'dokument_ob_obrazovanii_tip:tipDokumentaObObrazovanii:Тип документа',
        'kvalifikaciyaRel.nazvanie:text:Квалификация по документа',
        'dokument_ob_obrazovanii_data:date:Дата выдачи документа',
        [
            'class' => ActionColumn::className(),
            'template' => '{update}&nbsp;{delete}',
        ],
    ]
]) ?>
<?= Html::a('Добавить образование', ['create'], ['class' => 'btn btn-primary']) ?>