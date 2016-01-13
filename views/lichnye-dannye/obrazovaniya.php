<?php
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var $provider ActiveDataProvider
 */
?>
<?= GridView::widget([
    'dataProvider' => $provider,
    'layout' => '{items}',
    'columns' => [
        'organizaciyaRel.nazvanie:text:Образовательное учреждение',
        'dokument_ob_obrazovanii_tip:tipDokumentaObObrazovanii:Тип документа',
        'kvalifikaciyaRel.nazvanie:text:Квалификация по документа',
        'dokument_ob_obrazovanii_data:date:Дата выдачи документа',
        [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
            'urlCreator' => function ($action, $model, $key) {
                switch ($action) {
                    case 'update': return ['update-obrazovanie', 'id' => $key];
                    case 'delete': return ['delete-obrazovanie', 'id' => $key];
                }
                return '';
            }
        ],
    ]
]) ?>
<?= Html::a('Добавить образование', ['create-obrazovanie'], ['class' => 'btn btn-primary']) ?>