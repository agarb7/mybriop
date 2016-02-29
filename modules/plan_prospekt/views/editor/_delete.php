<?php
use app\modules\plan_prospekt\models\KursDelete;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $model KursDelete
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

    <?php if ($model->canBeDeleted): ?>

        <p><strong>Внимание!</strong> Данное действие необратимо. Вы действительно хотите удалить программу?</p>

        <div class="form-group form-group-buttons">
            <?= Html::submitButton('Удалить', ['class' => 'btn btn-primary']) ?>
            <?= Html::button('Отменить', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
        </div>

    <?php else: ?>

        <p><strong>Удаление невозможно:</strong> либо заполнена программа курса, либо записаны слушатели.</p>

        <div class="form-group form-group-buttons">
            <?= Html::button('Ок', ['class' => 'btn btn-primary', 'data-dismiss' => 'modal']) ?>
        </div>

    <?php endif ?>

    <?php ActiveForm::end() ?>

<?php endif ?>
