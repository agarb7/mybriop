<?php
use app\modules\plan_prospekt\models\KursForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $model KursForm
 * @var $modalTitle string
 */
?>

<?php if (isset($model)): ?>

    <?php $form = ActiveForm::begin(['options' => ['data' => [
        'pjax' => true,
        'modal' => [
            'title' => $modalTitle
        ]
    ]]]) ?>

    <?php if (!$model->iup): ?>

        <?= Html::activeHiddenInput($model, 'iup', ['value' => 1]) ?>

        <p>Сделать программу доступной для записи по ИУП?</p>

        <div class="form-group form-group-buttons">
            <?= Html::submitButton('Назначить ИУП', ['class' => 'btn btn-primary']) ?>
            <?= Html::button('Нет', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
        </div>

    <?php else: ?>

        <?= Html::activeHiddenInput($model, 'iup', ['value' => 0]) ?>

        <p>Отменить доступ к программе для записи по ИУП?</p>

        <div class="form-group form-group-buttons">
            <?= Html::submitButton('Отменить ИУП', ['class' => 'btn btn-primary']) ?>
            <?= Html::button('Нет', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
        </div>

    <?php endif ?>

    <?php ActiveForm::end() ?>

<?php endif ?>
