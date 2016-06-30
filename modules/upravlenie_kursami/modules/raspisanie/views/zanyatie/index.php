<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\upravlenie_kursami\raspisanie\models\KursForm;
use app\upravlenie_kursami\raspisanie\data\DayData;
use app\upravlenie_kursami\raspisanie\widgets\ZanyatieGrid;
use app\upravlenie_kursami\raspisanie\widgets\TemaPicker;
use app\upravlenie_kursami\raspisanie\widgets\PrepodavatelPeresechenieModal;

/**
 * @var $kursForm KursForm
 * @var $kursRecord KursForm
 * @var $gridData DayData
 * @var $auditorii array
 * @var $prepodavateli array
 */

$this->title = "БРИОП – расписание для «{$kursRecord->nazvanie}»";
?>

<h2 class="raspisanie-kurs-title">расписание для<br><span class="raspisanie-kurs-nazvanie"><?= Html::encode($kursRecord->nazvanie) ?></span></h2>

<?php $form = ActiveForm::begin([
    'options' => ['class' => 'raspisanie-kurs-form']
]); ?>

<div class="row">
    <div class="col-md-2 col-md-nachalo">
        <?= $form->field($kursForm, 'raspisanie_nachalo_input') ?>
    </div>
    <div class="col-md-2 col-md-konec">
        <?= $form->field($kursForm, 'raspisanie_konec_input') ?>
    </div>
    <div class="col-md-2 col-md-auditoriya">
        <?= $form->field($kursForm, 'auditoriya_po_umolchaniyu')->dropDownList($auditorii) ?>
    </div>
    <div class="col-md-2 col-md-btn">
        <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>

<?= ZanyatieGrid::widget([
    'data' => $gridData,
    'auditorii' => $auditorii,
    'prepodavateli' => $prepodavateli,
    'temaPickerSelector' => '#tema-picker',
    'prepodavatelPeresechenieModalSelector' => '#prepodavatel-peresechenie-modal',
    'zanyatieUpdateAction' => ['update', 'kurs' => $kursRecord->id],
    'zanyatieDeleteAction' => ['delete', 'kurs' => $kursRecord->id]
]) ?>

<?= TemaPicker::widget([
    'id' => 'tema-picker',
    'kurs' => $kursRecord,
    'temaIndexAction' => ['tema/index', 'kurs' => $kursRecord->id],
    'temaFilterOptionsAction' => ['tema/filter-options', 'kurs' => $kursRecord->id]
]) ?>

<?= PrepodavatelPeresechenieModal::widget([
    'id' => 'prepodavatel-peresechenie-modal',
    'prepodavatelPeresechenieAction' => ['prepodavatel-peresechenie', 'kurs' => $kursRecord->id]
]) ?>
