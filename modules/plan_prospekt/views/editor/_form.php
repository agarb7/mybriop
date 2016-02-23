<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php if (isset($model)): ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true]]) ?>

    <?= $form->field($model, 'nazvanie')->textInput() ?>

    <?= Html::submitButton() ?>

    <?php ActiveForm::end() ?>

<?php endif ?>

<?= Html::tag('div', null, ['class' => 'hidden data', 'data' => [
    'tohide' => isset($model) ? 0 : 1,
    'backurl' => isset($backUrl) ? $backUrl : null
]]) ?>
