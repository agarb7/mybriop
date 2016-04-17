<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="strukturnoe-podrazdelenie-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'organizaciya')->dropDownList(
    ArrayHelper::map($orgname, 'id', 'nazvanie'), ['prompt' => 'Выберите организацию...']) ?>

    <?= $form->field($model, 'nazvanie')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'obschij')->checkbox() ?>

    <?= $form->field($model, 'sokrashennoe_nazvanie')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
