<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\organizaciya\Organizaciya */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organizaciya-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nazvanie')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'adres_adresnyj_objekt')->textInput() ?>

    <?= $form->field($model, 'adres_dom')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'etapy_obrazovaniya')->textInput() ?>

    <?= $form->field($model, 'obschij')->checkbox() ?>

    <?= $form->field($model, 'vedomstvo')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
