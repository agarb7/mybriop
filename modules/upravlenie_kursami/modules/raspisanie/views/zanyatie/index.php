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
 * @var $user \app\entities\Polzovatel
 */

$this->title = "БРИОП – расписание для «{$kursRecord->nazvanie}»";

if (isset($_SESSION['success_msg'])){
    $this->registerJs('$(function(){bsalert('.$_SESSION['success_msg'].',\'success\');});');
    unset($_SESSION['success_msg']);
}

?>

<h2 class="raspisanie-kurs-title">расписание для<br><span class="raspisanie-kurs-nazvanie"><?= Html::encode($kursRecord->nazvanie) ?></span></h2>

<? if ($kursForm->status_raspisaniya == \app\enums2\StatusRaspisaniyaKursa::REDAKTIRUETSYA):?>
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
<? else: ?>
    <div class="row bottom-margin">
        <div class="col-md-3 col-md-nachalo">
            <p class="bold">Проводится с</p>
            <?= $kursForm->raspisanie_nachalo_input?>
        </div>
        <div class="col-md-3 col-md-konec">
            <p class="bold">По</p>
            <?= $kursForm->raspisanie_konec_input ?>
        </div>
        <div class="col-md-3 col-md-auditoriya">
            <p class="bold">Аудитория по-умолчанию</p>
            <?= $auditorii[$kursForm->auditoriya_po_umolchaniyu] ?>
        </div>
    </div>
<?endif?>

<?= ZanyatieGrid::widget([
    'kurs' => $kursForm,
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

<div class="row top-margin">
    <div class="col-md-12">
        <? if (!$kursForm->data_otpravki_v_uo):?>
            <a href="/upravlenie-kursami/raspisanie/zanyatie/send-to-uo?kurs=<?=$kursRecord->id?>" class="btn btn-primary">Отправить в учебный отдел</a>
        <?endif?>
        <?if ($user->isThereRol(\app\enums2\Rol::SOTRUDNIK_UCHEBNOGO_OTDELA)):?>
            <? if ($kursForm->status_raspisaniya == \app\enums2\StatusRaspisaniyaKursa::REDAKTIRUETSYA): ?>
                <a href="/upravlenie-kursami/raspisanie/zanyatie/sign-raspisanie?kurs=<?=$kursRecord->id?>" class="btn btn-primary">Подписать</a>
            <? else: ?>
                <a href="/upravlenie-kursami/raspisanie/zanyatie/unsign-raspisanie?kurs=<?=$kursRecord->id?>" class="btn btn-primary">Расподписать</a>
            <?endif?>
        <?endif?>
    </div>
</div>
