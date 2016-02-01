<?php
use app\helpers\Html;
use app\models\dolzhnost\DolzhnostModel;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\bootstrap\ActiveForm;

/**
 * @var $this View
 * @var $model DolzhnostModel
 * @var $actionCaption string
 */
?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']) ?>

<div class="row">
<div class="col-sm-offset-3 col-sm-6 alert alert-danger" role="alert"><strong>Внимание!</strong> Это действие невозможно отменить!</div>
</div>

<div class="form-group">
    <label class="control-label col-sm-3" for="dolzhnostmodel-name">Выбранные должности</label>
    <div class="col-sm-6">
        <?= Html::ul(ArrayHelper::getColumn($model->getDolzhnosti(), 'nazvanie')) ?>
    </div>
</div>

<?php foreach ($model->ids as $key => $id) echo Html::activeHiddenInput($model, "ids[$key]") ?>

<?= $form->field($model, 'name') ?>

<div class="row">
    <div class="col-sm-offset-3">
        <?= Html::submitButton($actionCaption, ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
