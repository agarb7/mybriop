<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenieSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$flash = \Yii::$app->session->getAllFlashes();
if ($flash){
    $js = '';
    foreach ($flash as $k => $v) {
        $js .= 'bsalert("'.$v.'","'.$k.'","top");'."\n";
    }
    $this->registerJS('$(function(){'.$js.'})');
}

$this->title = 'Справочник структурных подразделений';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="strukturnoe-podrazdelenie-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p><?= Html::a('Новое структурное подразделение', ['create'], ['class' => 'btn btn-success']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'organizaciyaNazvanie',
            'nazvanie',
            [
                'attribute' =>'obschij',
                'filter' => ['0' => 'Нет', '1' => 'Да'],
                'value' => function($data) {
                    if ($data->obschij) return 'Да';
                    else return 'Нет';
                }
            ],
            'sokrashennoe_nazvanie',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
