<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie;

/* @var $this yii\web\View */
/* @var $searchModel app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenieSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Справочник структурных подразделений';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="strukturnoe-podrazdelenie-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новое структурное подразделение', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'organizaciyaNazvanie',
            'nazvanie',
            'obschij:boolean',
            'sokrashennoe_nazvanie',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
