<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie */

$this->title = 'Новое структурное подразделение';
$this->params['breadcrumbs'][] = ['label' => 'Strukturnoe Podrazdelenies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="strukturnoe-podrazdelenie-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
