<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenieSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="strukturnoe-podrazdelenie-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'organizaciyaNazvanie') ?>

    <?= $form->field($model, 'nazvanie') ?>

    <?= $form->field($model, 'obschij')->checkbox() ?>

    <?= $form->field($model, 'sokrashennoe_nazvanie') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
