<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 24.10.2017
 * Time: 14:15
 */

use yii\widgets\MaskedInput;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\helpers\Html;
use app\entities\EntityQuery;
use app\entities\Dolzhnost;
use app\widgets\SwitchingFields;
use app\enums2\Rol;
use app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie;
use app\helpers\ArrayHelper;
use app\enums2\OrgTipRaboty;

$this->title = 'Перевод';

?>

<div class="row">
    <h3>Cотрудник <?= $sotrudnik->fio ?></h3>

<? $form = ActiveForm::begin([
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-md-8\">\n{input}\n{hint}\n{error}\n</div>",
        'labelOptions' => ['class' => 'control-label col-md-4'],
    ]]);
    echo $form->field($sotrudnik, 'fizLicoId',['template' => "{input}"])->hiddenInput();
    echo $form->field($sotrudnik, 'dolzhnostFizLicaNaRaboteId',['template' => "{input}"])->hiddenInput();
?>

    <div class="col-md-6 form-horizontal">
        <div class="fields-group-heading">
            <h3>Перевод в другое подразделение</h3>
        </div>

        <?= $form->field($sotrudnik, 'strukturnoePodrazdelenie')->widget(Select2::className(),[
            'data' => ArrayHelper::map(StrukturnoePodrazdelenie::find()->where(['obschij' => true, 'organizaciya' => 1, 'actual' => true])->orderBy('nazvanie')->asArray()->all(), 'id', 'nazvanie'),
            'options' => ['placeholder' => 'Выберите подразделение'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>

        <?= SwitchingFields::widget([
            'commonOptions' => [
                'form' => $form,
                'model' => $sotrudnik,
                'options' => [
                    'template' => "{label}\n<div class=\"col-md-8\">\n{input}\n{hint}\n{error}\n{switch}\n</div>",
                    'options' => ['class' => 'form-group']
                ]
            ],
            'field1Options' => [
                'attribute' => 'rabotaDolzhnostId',
                'switchIntroText' => 'Не нашли в списке?',
                'switchLinkText' => 'Нажмите здесь чтобы ввести должность вручную.',

                'widgetClass' => Select2::className(),
                'widgetConfig' => [
                    'data' => Dolzhnost::find()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
                ],
                'widgetConfigDisabled' => ['disabled' => true]
            ],
            'field2Options' => [
                'attribute' => 'rabotaDolzhnostNazvanie',
                'switchIntroText' => 'Внимание! Будет создана новая запись в общедоступном справочнике должностей.',
                'switchLinkText' => 'Выбрать из списка.'
            ]
        ]) ?>

        <?= $form->field($sotrudnik, 'rukovoditelPodrazdeleniya')->checkbox([], false) ?>
        <?= $form->field($sotrudnik, 'stazh') ?>
        <?= $form->field($sotrudnik, 'rabotaOrgTip')->dropDownList(OrgTipRaboty::names(), ['prompt' => 'Выберите вид занятости']) ?>
        <?= $form->field($sotrudnik, 'rabotaDolyaStavki')->textInput(['type' => 'number', 'min' => 0, 'max' => 1.5, 'step' => 0.01]) ?>
        <?= $form->field($sotrudnik, 'rabotaTelefon')->widget(MaskedInput::className(), ['mask' => '+79999999999']) ?>
    </div>

    <div class="col-md-4 col-md-offset-3">
        <?= Html::submitButton(
            'Сохранить',
            ['class' => 'btn btn-primary btn-block']
        ) ?>
    </div>
</div>

<?php ActiveForm::end() ?>