<?php

use app\entities\Dolzhnost;
use app\entities\EntityQuery;
use app\entities\Kurs;
use app\entities\Kvalifikaciya;
use app\entities\Organizaciya;
use app\enums\KategoriyaPedRabotnika;
use app\enums\TipDokumentaObObrazovanii;
use app\models\kurs_slushatelyu\ZapisNaKursForm;
use app\widgets\DeprecatedDatePicker;
use app\widgets\KursSummary;
use app\widgets\PasportNomerInput;
use app\widgets\SwitchingFields;
use app\widgets\TouchSpin;
use kartik\widgets\ActiveForm;

use app\helpers\Html;
use kartik\widgets\Select2;
use yii\widgets\MaskedInput;
use app\helpers\ArrayHelper;

/**
 * @var ZapisNaKursForm $model
 * @var $this yii\web\View
 */

$this->title = 'Запись на курс в БРИОП';

$form = ActiveForm::begin([
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-md-8\">\n{input}\n{hint}\n{error}\n</div>",
        'labelOptions' => ['class' => 'control-label col-md-4'],
    ]
]);

$kursEntity = Kurs::findOne($kurs);

?>

<h2>Запись на курс «<?= $kursEntity->nazvanie ?>» </h2>

<?= KursSummary::widget([
    'model' => $kursEntity
]) ?>

<div class="row">

    <div class="col-md-5 form-horizontal">
        <div class="fields-group-heading">
            <h3>Личные данные</h3>
        </div>

        <?= $form->field($model, 'dataRozhdeniya')->widget(DeprecatedDatePicker::classname()) ?>

        <?= $form->field($model, 'pol')->dropDownList([
           '0' => 'жен',
           '1' => 'муж'
        ], ['prompt' => 'необходимо выбрать']) ?>

        <?= $form->field($model, 'uchenajaStepen')->widget(Select2::className(), ['data' => ArrayHelper::map($uchenajaStepenRecords,'id','nazvanie')])?>
        
    </div>

    <div class="col-md-5 form-horizontal">
        <div class="fields-group-heading">
            <h3>Работа</h3>
        </div>

        <?= $form->field($model, 'obshhijStazh')->widget(TouchSpin::className()) ?>

        <?= $form->field($model, 'pedStazh')->widget(TouchSpin::className()) ?>

        <?= $form->field($model, 'stazhVDolzhnosti')->widget(TouchSpin::className()) ?>

        <?= $form->field($model, 'kategoriya')->widget(Select2::className(), [
            'data' => KategoriyaPedRabotnika::namesMap()
        ]) ?>

        <?= SwitchingFields::widget([
            'commonOptions' => [
                'form' => $form,
                'model' => $model,
                'options' => [
                    'template' => "{label}\n<div class=\"col-md-8\">\n{input}\n{hint}\n{error}\n{switch}\n</div>",
                    'options' => ['class' => 'form-group']
                ]
            ],
            'field1Options' => [
                'attribute' => 'dolzhnostId',
                'switchIntroText' => 'Не нашли в списке?',
                'switchLinkText' => 'Нажмите здесь чтобы ввести должность вручную.',

                'widgetClass' => Select2::className(),
                'widgetConfig' => [
                    'data' => Dolzhnost::find()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
                ],
                'widgetConfigDisabled' => ['disabled' => true]
            ],
            'field2Options' => [
                'attribute' => 'dolzhnostNazvanie',
                'switchIntroText' => 'Возможно ваша должность есть в списке.',
                'switchLinkText' => 'Нажмите здесь чтобы найти её в списке.'
            ]
        ]) ?>

    </div>

    <div class="col-md-5 form-horizontal">
        <div class="fields-group-heading">
            <h3>Образование</h3>
        </div>

        <?= SwitchingFields::widget([
            'commonOptions' => [
                'form' => $form,
                'model' => $model,
                'options' => [
                    'template' => "{label}\n<div class=\"col-md-8\">\n{input}\n{hint}\n{error}\n{switch}\n</div>",
                    'options' => ['class' => 'form-group']
                ]
            ],
            'field1Options' => [
                'attribute' => 'obrOrgId',
                'switchIntroText' => 'Не нашли в списке?',
                'switchLinkText' => 'Нажмите здесь чтобы ввести организацию вручную.',

                'widgetClass' => Select2::className(),
                'widgetConfig' => [
                    'data' =>
                        Organizaciya::findVysshegoProfessionalnogoObrazovaniya()
                        ->commonOnly()
                        ->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
                ],
                'widgetConfigDisabled' => ['disabled' => true]
            ],
            'field2Options' => [
                'attribute' => 'obrOrgNazvanie',
                'switchIntroText' => 'Возможно ваша организация есть в списке.',
                'switchLinkText' => 'Нажмите здесь чтобы найти её в списке.'
            ]
        ]) ?>

        <?= SwitchingFields::widget([
            'commonOptions' => [
                'form' => $form,
                'model' => $model,
                'options' => [
                    'template' => "{label}\n<div class=\"col-md-8\">\n{input}\n{hint}\n{error}\n{switch}\n</div>",
                    'options' => ['class' => 'form-group']
                ]
            ],
            'field1Options' => [
                'attribute' => 'obrKvalifikaciyaId',
                'switchIntroText' => 'Не нашли в списке?',
                'switchLinkText' => 'Нажмите здесь чтобы ввести квалификацию вручную.',

                'widgetClass' => Select2::className(),
                'widgetConfig' => [
                    'data' => Kvalifikaciya::find()
                        ->commonOnly()
                        ->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
                ],
                'widgetConfigDisabled' => ['disabled' => true]
            ],
            'field2Options' => [
                'attribute' => 'obrKvalifikaciyaNazvanie',
                'switchIntroText' => 'Возможно ваша квалификация есть в списке.',
                'switchLinkText' => 'Нажмите здесь чтобы найти её в списке.'
            ]
        ]) ?>

        <?= $form->field($model, 'obrDocTip')->widget(Select2::className(), [
            'data' => TipDokumentaObObrazovanii::namesMap()
        ]) ?>

        <?= $form->field($model, 'obrDocSeriya') ?>
        <?= $form->field($model, 'obrDocNomer') ?>
        <?= $form->field($model, 'obrDocData')->widget(DeprecatedDatePicker::classname()) ?>

    </div>

</div>

<?php if ($model->scenario === ZapisNaKursForm::SCENARIO_ZAPIS_VNEBYUDZHET): ?>
    <div class="row">

        <div class="col-md-5 form-horizontal">
            <div class="fields-group-heading">
                <h3>Паспортные данные</h3>
            </div>

            <?= $form->field($model, 'pasportNomer')->widget(PasportNomerInput::className()) ?>

            <?= $form->field($model, 'pasportKemVydanKod')->widget(
                MaskedInput::className(),
                ['mask' => '999-999']
            ) ?>

            <?= $form->field($model, 'pasportKemVydan') ?>
            <?= $form->field($model, 'pasportKogdaVydan')->widget(DeprecatedDatePicker::classname()) ?>

        </div>

        <div class="col-md-5 form-horizontal">
            <div class="fields-group-heading">
                <h3>Другие данные</h3>
            </div>

            <?= $form->field($model, 'propiska') ?>
            <?= $form->field($model, 'snils')->widget(
                MaskedInput::className(),
                ['mask' => '999-999-999-99']
            ) ?>
            <?= $form->field($model, 'inn')->widget(
                MaskedInput::className(),
                ['mask' => '999999999999']
            ) ?>

        </div>

    </div>
<?php endif ?>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="row form-buttons">
            <div class="col-md-4 col-md-offset-2">
                <?= Html::submitButton('Записаться', ['class' => 'btn btn-primary btn-block']) ?>
            </div>
            <div class="col-md-4">
                <?= Html::returningA('Отменить', ['class' => 'btn btn-default btn-block']) ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>
