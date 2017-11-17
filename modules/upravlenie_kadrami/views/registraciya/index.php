<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 14.05.2016
 * Time: 22:10
 */

namespace app\modules\upravlenie_kadrami;

use app\records\FizLico;
use yii\jui\AutoComplete;
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
use yii\web\JsExpression;

$this->title = 'Регистрация нового сотрудника';

Asset::register($this);

$form = ActiveForm::begin([
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-md-8\">\n{input}\n{hint}\n{error}\n</div>",
        'labelOptions' => ['class' => 'control-label col-md-4'],
    ]
]);

?>
<div class="row">
    <h3>Регистрация нового сотрудника</h3>
    <div class="col-md-6 form-horizontal">
        <div class="fields-group-heading">
            <h3>Личные данные</h3>
        </div>

        <?= $form->field($model, 'fizLicoId', ['template' => "{input}"])->hiddenInput(); ?>

        <div id="new-person" style="display: block">
            <?= $form->field($model, 'familiya')->widget(AutoComplete::className(),[
                'clientOptions' => [
                    'source' => FizLico::find()->select(['familiya as value', 'concat_ws(\' \', familiya, imya, otchestvo) as label', 'id as id', ])->asArray()->all(),
                    'select' => new JsExpression("function( event, ui ) {
                        var flid = ui.item.id;
                        $('#sotrudnik-fizlicoid').val(flid);
                        $('#new-person').hide();
                        $('#roli').hide();
                        ui.item.value = '';
                        if(flid){
                            showLoader();
                            $.post(\"./person?id=\"+flid,
                            function( data ) {
                                $( \"div#old-person\" ).html( data );
                                hideLoader();
                            });
                        }else{
                            $(\"div#old-person\").empty();
                        };
                    }"),
                    'minLength' => '3',
                ],
                'options' => [
                    'placeholder' => 'Выберите из списка зарегестрированных пользователей',
                    'class' => 'form-control'
                ]
                ])
            ?>
            <?= $form->field($model, 'imya') ?>
            <?= $form->field($model, 'otchestvo') ?>
            <?= $form->field($model, 'telefon')->widget(MaskedInput::className(), ['mask' => '+79999999999']) ?>

            <div class="fields-group-heading">
                <h3>Данные для входа на сайт</h3>
            </div>

            <?= $form->field($model, 'email') ?>
            <?= $form->field($model, 'login') ?>
            <?= $form->field($model, 'parol')->passwordInput() ?>
            <?= $form->field($model, 'podtverzhdenieParolya')->passwordInput() ?>
        </div>
        
        <div id="old-person"></div>

        <div class="fields-group-heading">
            <h3>Работа</h3>
        </div>

        <?= $form->field($model, 'strukturnoePodrazdelenie')->widget(Select2::className(),[
            'data' => ArrayHelper::map(StrukturnoePodrazdelenie::find()->where(['obschij' => true, 'organizaciya' => 1, 'actual' => true])->orderBy('nazvanie')->asArray()->all(), 'id', 'nazvanie'),
            'options' => ['placeholder' => 'Выберите подразделение'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]); ?>
        
        <?= $form->field($model, 'tipDogovora')->dropDownList(\app\enums2\TipDogovoraRaboty::names(), ['onchange'=>'tip_dogovora()', 'prompt' => 'Выберите тип договора']) ?>

        <div id="trud" style="display: none">
            <?= SwitchingFields::widget([
                'commonOptions' => [
                    'form' => $form,
                    'model' => $model,
                    'options' => [
                        'template' => "{label}\n<div class=\"col-md-8\">\n{input}\n{hint}\n{error}\n{switch}\n</div>",
                        'options' => ['class' => 'form-group'],
                    ],
                ],
                'field1Options' => [
                    'attribute' => 'rabotaDolzhnostId',
                    'switchIntroText' => 'Не нашли в списке?',
                    'switchLinkText' => 'Нажмите здесь чтобы ввести должность вручную.',

                    'widgetClass' => Select2::className(),
                    'widgetConfig' => [
                        'data' => Dolzhnost::find()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie'),
                        'options' => ['placeholder' => 'Выберите должность']
                    ],
                    'widgetConfigDisabled' => ['disabled' => true],
                ],
                'field2Options' => [
                    'attribute' => 'rabotaDolzhnostNazvanie',
                    'switchIntroText' => 'Внимание! Будет создана новая запись в общедоступном справочнике должностей.',
                    'switchLinkText' => 'Выбрать из списка'
                ]
            ]) ?>

            <?= $form->field($model, 'rukovoditelPodrazdeleniya')->checkbox([], false) ?>
            <?= $form->field($model, 'stazh') ?>
            <?= $form->field($model, 'rabotaOrgTip')->dropDownList(OrgTipRaboty::names(), ['prompt' => 'Выберите вид занятости']) ?>
            <?= $form->field($model, 'rabotaDolyaStavki')->textInput(['type' => 'number', 'min' => 0, 'max' => 1.5, 'step' => 0.01]) ?>
            <?= $form->field($model, 'rabotaTelefon')->widget(MaskedInput::className(), ['mask' => '+79999999999']) ?>
        </div>

        <div id="roli" style="display: block">
            <div class="fields-group-heading">
                <h3>Роли пользователя в системе</h3>
            </div>
    
            <?= $form->field($model, 'roli')->widget(Select2::classname(), [
                'data' => Rol::names(),
                'options' => ['multiple' => true, 'placeholder' => 'Выберите роль ...'],
            ]) ?>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="fields-group-heading"></div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <?= Html::submitButton(
                    'Зарегистрировать',
                    ['class' => 'btn btn-primary btn-block']
                ) ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>
