<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\enums\EtapObrazovaniya;

/* @var $this yii\web\View */
/* @var $searchModel app\models\organizaciya\OrganizaciyaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$flash = \Yii::$app->session->getAllFlashes();
if ($flash){
    $js = '';
    foreach ($flash as $k => $v) {
        $js .= 'bsalert("'.$v.'","'.$k.'","top");'."\n";
    }
    $this->registerJS('$(function(){'.$js.'})');
}

$this->title = 'Справочник организаций';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizaciya-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Новая организация', ['create'], ['class' => 'btn btn-success']) ?></p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'nazvanie',
            'organizaciyaAdres',
            [
                'attribute' => 'etapyObrazovaniyaSpisok',
                'filter' => EtapObrazovaniya::namesMap(),
            ],
            [
                'attribute' =>'obschij',
                'filter' => ['0' => 'Нет', '1' => 'Да'],
                'value' => function($data) {
                    if ($data->obschij) return 'Да';
                    else return 'Нет';
                }
            ],
            'vedomstvoNazvanie',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
