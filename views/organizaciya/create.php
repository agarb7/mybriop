<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\organizaciya\Organizaciya */

$this->title = 'Новая организация';
$this->params['breadcrumbs'][] = ['label' => 'Organizaciyas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizaciya-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
