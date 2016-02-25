<?php
use app\enums2\TipKursa;
use app\modules\plan_prospekt\models\KursSearch;
use app\widgets\DatePicker;
use app\widgets\TouchSpin;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $searchModel KursSearch
 * @var $formActionUrl string
 * @var $kategoriiSlushatelej array
 * @var $rukovoditeliKursov array
 */
?>

<?php $form = ActiveForm::begin([
    'method' => 'get',
    'action' => $formActionUrl
]) ?>

<?= $form->field($searchModel, 'tip')->widget(Select2::className(), [
    'data' => TipKursa::names(),
    'options' => ['placeholder' => ''],
//    'hideSearch' => true,
    'pluginOptions' => ['allowClear' => true],
]) ?>

<?= $form->field($searchModel, 'kategorii_slushatelej')->widget(Select2::className(), [
    'data' => $kategoriiSlushatelej,
    'options' => ['multiple' => true],
    'pluginOptions' => ['allowClear' => true],
]) ?>

<?= $form->field($searchModel, 'nazvanie')->textInput() ?>

<?= $form->field($searchModel, 'rukovoditel')->widget(Select2::className(), [
    'data' => $rukovoditeliKursov,
    'options' => ['placeholder' => ''],
    'pluginOptions' => ['allowClear' => true]
]) ?>

<?= $form->field($searchModel, 'raschitano_chasov')->widget(TouchSpin::className()) ?>

<?= $form->field($searchModel, 'nachnutsya_posle')->widget(DatePicker::className()) ?>

<?= $form->field($searchModel, 'zakonchatsya_do')->widget(DatePicker::className()) ?>

<?= Html::submitButton() ?>

<?php ActiveForm::end() ?>