<?php
use app\modules\plan_prospekt\models\KursForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model KursForm */
?>

<?php if (isset($model)): ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true]]) ?>

    <?php if (!$model->iup): ?>

        <?= Html::activeHiddenInput($model, 'iup', ['value' => 1]) ?>

        <p>Сделать программу доступной для записи по ИУП?</p>

    <?php else: ?>

        <?= Html::activeHiddenInput($model, 'iup', ['value' => 0]) ?>

        <p>Отменить доступ к программе для записи по ИУП?</p>

    <?php endif ?>

    <?= Html::submitButton() ?>

    <?php ActiveForm::end() ?>

<?php endif ?>

<?= Html::tag('div', null, ['class' => 'hidden data', 'data' => [
    'tohide' => isset($model) ? 0 : 1,
    'backurl' => isset($backUrl) ? $backUrl : null
]]) ?>

