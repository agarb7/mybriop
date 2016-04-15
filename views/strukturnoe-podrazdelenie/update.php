<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie */

$this->title = 'Редактировать структурное подразделение: ' . ' ' . $model->nazvanie;
$this->params['breadcrumbs'][] = ['label' => 'Strukturnoe Podrazdelenies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="strukturnoe-podrazdelenie-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'orgname' => $orgname,
    ]) ?>

</div>
