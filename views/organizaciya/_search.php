<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\organizaciya\OrganizaciyaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organizaciya-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nazvanie') ?>

    <?= $form->field($model, 'adres_adresnyj_objekt') ?>

    <?= $form->field($model, 'adres_dom') ?>

    <?= $form->field($model, 'etapy_obrazovaniya') ?>

    <?php // echo $form->field($model, 'obschij')->checkbox() ?>

    <?php // echo $form->field($model, 'vedomstvo') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
