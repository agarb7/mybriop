<?php
use app\modules\plan_prospekt\models\KursDelete;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $model KursDelete */
?>

<?php if (isset($model)): ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true]]) ?>

        <?php if ($model->canBeDeleted): ?>

            <p><strong>Внимание!</strong> Данное действие необратимо. Вы действительно хотите удалить программу?</p>

            <?= Html::submitButton() ?>

        <?php else: ?>

            <p>Удаление невозможно: либо заполнена программа курса, либо записаны слушатели.</p>

            <?= Html::button('Ок', ['data-dismiss' => 'modal']) ?>

        <?php endif ?>

    <?php ActiveForm::end() ?>

<?php endif ?>

<?= Html::tag('div', null, ['class' => 'hidden data', 'data' => [
    'tohide' => isset($model) ? 0 : 1,
    'backurl' => isset($backUrl) ? $backUrl : null
]]) ?>

