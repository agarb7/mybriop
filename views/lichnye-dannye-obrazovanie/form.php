<?php
use app\entities\EntityQuery;
use app\entities\Kvalifikaciya;
use app\entities\Organizaciya;
use app\enums\TipDokumentaObObrazovanii;
use app\enums\TipKursa;
use app\models\lichnye_dannye_obrazovanie\ObrazovanieForm;
use app\widgets\ComboWidget;
use app\widgets\DatePicker;
use app\widgets\TouchSpin;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $model ObrazovanieForm
 */
?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']) ?>
<?= $form->field($model, 'dokument_ob_obrazovanii_tip')->dropDownList(TipDokumentaObObrazovanii::namesMap()) ?>
<?= $form->field($model, 'dokument_ob_obrazovanii_seriya') ?>
<?= $form->field($model, 'dokument_ob_obrazovanii_nomer') ?>
<?= $form->field($model, 'dokument_ob_obrazovanii_data')->widget(DatePicker::className()) ?>

<?= $form->field($model, 'kvalifikaciyaDir')->widget(ComboWidget::className(), [
    'data' => Kvalifikaciya::find()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
]) ?>

<?= $form->field($model, 'organizaciyaDir')->widget(ComboWidget::className(), [
    'data' => Organizaciya::find()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
]) ?>

<?= $form->field($model, 'kurs_tip')->widget(Select2::className(), [
    'data' => TipKursa::namesMap(),
    'hideSearch' => true,
    'options' => ['placeholder' => ''],
    'pluginOptions' => ['allowClear' => true]
]) ?>

<?= $form->field($model, 'kurs_nazvanie') ?>
<?= $form->field($model, 'kurs_chasy')->widget(TouchSpin::className()) ?>

<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>
