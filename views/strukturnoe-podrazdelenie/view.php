<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie */

$this->title = $model->nazvanie;
$this->params['breadcrumbs'][] = ['label' => 'Strukturnoe Podrazdelenies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="strukturnoe-podrazdelenie-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить подразделение?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'organizaciyaNazvanie',
            'nazvanie',
            'obschij:boolean',
            'sokrashennoe_nazvanie',
        ],
    ]) ?>

</div>
