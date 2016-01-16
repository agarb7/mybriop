<?php
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var $data ActiveDataProvider
 * @var $rabota string
 */
?>
<?= GridView::widget([
    'dataProvider' => $data,
    'layout' => '{items}',
    'columns' => [
        'dolzhnostRel.nazvanie:text:Должность',
        'org_tip:orgTipDolzhnosti:Совмещение',
        'etap_obrazovaniya:etapObrazovaniya:Этап образования',
        'stazh:text:Стаж в должности',
        [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
        ],
    ]
]) ?>
<?= Html::a('Добавить должность', ['create', 'rabota' => $rabota], ['class' => 'btn btn-primary']) ?>