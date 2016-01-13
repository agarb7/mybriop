<?php
use app\entities\EntityQuery;
use app\entities\Kvalifikaciya;
use app\entities\Organizaciya;
use app\enums\TipDokumentaObObrazovanii;
use app\enums\TipKursa;
use app\models\lichnye_dannye\ObrazovanieForm;
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
<?= $form->field($model, 'dokumentTip')->dropDownList(TipDokumentaObObrazovanii::namesMap()) ?>
<?= $form->field($model, 'dokumentSeriya') ?>
<?= $form->field($model, 'dokumentNomer') ?>
<?= $form->field($model, 'dokumentData')->widget(DatePicker::className()) ?>

<?= $form->field($model, 'kvalifikaciya')->widget(ComboWidget::className(), [
    'data' => Kvalifikaciya::find()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
]) ?>

<?= $form->field($model, 'organizaciya')->widget(ComboWidget::className(), [
    'data' => Organizaciya::find()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
]) ?>

<?= $form->field($model, 'kursTip')->widget(Select2::className(), [
    'data' => TipKursa::namesMap(),
    'hideSearch' => true,
    'options' => ['placeholder' => ''],
    'pluginOptions' => ['allowClear' => true]
]) ?>

<?= $form->field($model, 'kursNazvanie') ?>
<?= $form->field($model, 'kursChasy')->widget(TouchSpin::className()) ?>

<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>
