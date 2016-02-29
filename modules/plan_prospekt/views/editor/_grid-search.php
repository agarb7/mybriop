<?php
use app\enums2\TipKursa;
use app\modules\plan_prospekt\models\KursSearch;
use app\widgets\DatePicker;
use app\widgets\TouchSpin;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap\ActiveForm;

/**
 * @var $this View
 * @var $searchModel KursSearch
 * @var $formActionUrl string
 * @var $kategoriiSlushatelej array
 * @var $rukovoditeliKursov array
 */

$this->registerJs('mybriop.planProspektEditor.gridSearchInit(".grid-search-container");');
?>

<div class = 'grid-search-container'>

<a href="#" class="grid-search-switch" data-pjax="0">Фильтры</a>

<?php $form = ActiveForm::begin([
    'options' => [
        'class' => 'grid-search',
        'style' => $searchModel->hasValues() ? null : 'display:none'
    ],
    'method' => 'get',
    'action' => $formActionUrl,

    'layout' => 'horizontal',
    'fieldConfig' => ['horizontalCssClasses' => [
        'label' => 'col-sm-3',
        'wrapper' => 'col-sm-9',
    ]],
]) ?>

<div class="col-md-6">

    <?= $form->field($searchModel, 'tip')->widget(Select2::className(), [
        'data' => TipKursa::names(),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true],
    ]) ?>

    <?= $form->field($searchModel, 'nazvanie')->textInput() ?>

    <?= $form->field($searchModel, 'kategorii_slushatelej')->widget(Select2::className(), [
        'data' => $kategoriiSlushatelej,
        'options' => ['multiple' => true],
        'pluginOptions' => ['allowClear' => true],
    ]) ?>

</div>

<div class="col-md-6">

    <?= $form->field($searchModel, 'rukovoditel')->widget(Select2::className(), [
        'data' => $rukovoditeliKursov,
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true]
    ]) ?>

    <?= $form->field($searchModel, 'raschitano_chasov')->widget(TouchSpin::className()) ?>

    <?= $form->field($searchModel, 'nachnutsya_posle')->widget(DatePicker::className()) ?>

    <?= $form->field($searchModel, 'zakonchatsya_do')->widget(DatePicker::className()) ?>

</div>

<?= Html::submitButton('Применить фильтры', ['class' => 'btn btn-primary']) ?>

<?= Html::a('Сбросить фильтры', $formActionUrl, ['class' => 'btn btn-default']) ?>

<?php ActiveForm::end() ?>

</div>