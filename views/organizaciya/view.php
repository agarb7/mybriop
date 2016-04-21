<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\organizaciya\Organizaciya */

$this->title = $model->nazvanie;
$this->params['breadcrumbs'][] = ['label' => 'Organizaciyas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizaciya-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'nazvanie',
            'adres_adresnyj_objekt',
            'adres_dom',
            //'etapy_obrazovaniya',
            'etapyObrazovaniyaSpisok',
            'obschij:boolean',
            'vedomstvoNazvanie',
        ],
    ]) ?>

</div>
