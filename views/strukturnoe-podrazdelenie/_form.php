<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use app\models\organizaciya\Organizaciya;

/* @var $this yii\web\View */
/* @var $model app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="strukturnoe-podrazdelenie-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'organizaciya')->widget(Select2::className(),[
        'data' => ArrayHelper::map(Organizaciya::find()->where(['obschij' => true])->orderBy('nazvanie')->asArray()->all(), 'id', 'nazvanie'),
        'options' => ['placeholder' => 'Выберите организацию'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'nazvanie')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'obschij')->checkbox() ?>

    <?= $form->field($model, 'sokrashennoe_nazvanie')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
