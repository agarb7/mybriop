<?php
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use yii\widgets\ActiveForm;

use app\upravlenie_kursami\raspisanie\models\KursForm;
use app\upravlenie_kursami\raspisanie\data\DayData;
use app\upravlenie_kursami\raspisanie\widgets\ZanyatieGrid;
use app\upravlenie_kursami\raspisanie\widgets\TemaPicker;

/**
 * @var $kursForm KursForm
 * @var $kursRecord KursForm
 * @var $gridData DayData
 * @var $auditorii array
 * @var $prepodavateli array
 */
?>

<?php $form = ActiveForm::begin([
//    'layout' => 'horizontal',
//    'fieldConfig' => [
//        'labelOptions' => ['class' => null]
//    ]
]); ?>

<div class="row">
    <div class="col-md-3">
        <?= $form->field($kursForm, 'raspisanie_nachalo_input') ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($kursForm, 'raspisanie_konec_input') ?>
    </div>
    <div class="col-md-3">
        <?= $form->field($kursForm, 'auditoriya_po_umolchaniyu')->dropDownList($auditorii) ?>
    </div>
    <div class="col-md-3">
        <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>

<?= ZanyatieGrid::widget([
    'data' => $gridData,
    'auditorii' => $auditorii,
    'prepodavateli' => $prepodavateli,
    'temaPickerSelector' => '#tema-picker',
    'zanyatieUpdateAction' => ['update', 'kurs' => $kursRecord->id],
    'zanyatieDeleteAction' => ['delete', 'kurs' => $kursRecord->id]
]) ?>

<?= TemaPicker::widget([
    'id' => 'tema-picker',
    'kurs' => $kursRecord,
    'temaIndexAction' => ['tema/index', 'kurs' => $kursRecord->id],
    'temaFilterOptionsAction' => ['tema/filter-options', 'kurs' => $kursRecord->id]
]) ?>
