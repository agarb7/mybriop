<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

use kartik\select2\Select2;

use app\enums2\FormaObucheniya;
use app\enums2\TipFinansirovaniya;
use app\enums2\TipKursa;
use app\widgets\DatePicker;
use app\widgets\TouchSpin;

use app\modules\plan_prospekt\models\KursForm;

/**
 * @var $this View
 * @var $model KursForm
 * @var $kategoriiSlushatelej array
 * @var $rukovoditeliKursov array
 * @var $modalSize string
 * @var $modalTitle string
 */
?>

<?php if (isset($model)): ?>

    <?php $form = ActiveForm::begin(['options' => ['data' => [
        'pjax' => true,
        'modal' => [
            'size' => $modalSize,
            'title' => $modalTitle
        ]
    ]]]) ?>

    <?= $form->field($model, 'nazvanie')->textarea() ?>

    <?= $form->field($model, 'annotaciya')->textarea(['rows' => 4]) ?>

    <fieldset>

        <div class="row">

            <?= $form->field($model, 'kategorii_slushatelej', [
                'options' => ['class' => 'form-group col-md-6']
            ])->widget(Select2::className(), [
                'data' => $kategoriiSlushatelej,
                'options' => ['placeholder' => '',  'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>

            <?= $form->field($model, 'raschitano_slushatelej', [
                'options' => ['class' => 'form-group col-md-6']
            ])->widget(TouchSpin::className()) ?>

        </div>

        <div class="row">

            <?= $form->field($model, 'tip', [
                'options' => ['class' => 'form-group col-md-6']
            ])->widget(Select2::className(), [
                'data' => TipKursa::names(),
                'options' => ['placeholder' => ''],
//        'hideSearch' => true,
                'pluginOptions' => ['allowClear' => true],
            ]) ?>

            <?= $form->field($model, 'formy_obucheniya_widget', [
                'options' => ['class' => 'form-group col-md-6']
            ])->widget(Select2::className(), [
                'data' => FormaObucheniya::names(),
                'options' => ['placeholder' => '',  'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
//        'hideSearch' => true,
            ]) ?>

        </div>

        <div class="row">

            <?= $form->field($model, 'raschitano_chasov', [
                'options' => ['class' => 'form-group col-md-4'],
            ])->widget(TouchSpin::className()) ?>

            <?= $form->field($model, 'finansirovanie', [
                'options' => ['class' => 'form-group col-md-4']
            ])->widget(Select2::className(), [
                'data' => TipFinansirovaniya::names(),
                'options' => ['placeholder' => ''],
//        'hideSearch' => true,
                'pluginOptions' => ['allowClear' => true],
            ]) ?>

            <?= $form->field($model, 'rukovoditel', [
                'options' => ['class' => 'form-group col-md-4']
            ])->widget(Select2::className(), [
                'data' => $rukovoditeliKursov,
                'options' => ['placeholder' => ''],
                'pluginOptions' => ['allowClear' => true]
            ]) ?>

        </div>

    </fieldset>

    <fieldset>

        <div class="row">

            <?= $form->field($model, 'ochnoe_nachalo', [
                'options' => ['class' => 'form-group col-md-3'],
            ])->widget(DatePicker::className(), [
                'pluginOptions' => ['orientation' => 'bottom']
            ]) ?>

            <?= $form->field($model, 'ochnoe_konec', [
                'options' => ['class' => 'form-group col-md-3']
            ])->widget(DatePicker::className(), [
                'pluginOptions' => ['orientation' => 'bottom']
            ]) ?>

            <?= $form->field($model, 'zaochnoe_nachalo', [
                'options' => ['class' => 'form-group col-md-3']
            ])->widget(DatePicker::className(), [
                'pluginOptions' => ['orientation' => 'bottom']
            ]) ?>

            <?= $form->field($model, 'zaochnoe_konec', [
                'options' => ['class' => 'form-group col-md-3']
            ])->widget(DatePicker::className(), [
                'pluginOptions' => ['orientation' => 'bottom']
            ]) ?>

        </div>

    </fieldset>

    <div class="form-group form-group-buttons">
        <?= Html::submitButton('Применить', ['class' => 'btn btn-primary']) ?>
        <?= Html::button('Отменить', ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) ?>
    </div>

    <?php ActiveForm::end() ?>

<?php endif ?>