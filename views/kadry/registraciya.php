<?php
/**
 * Created by PhpStorm.
 * User: asv
 * Date: 14.05.2016
 * Time: 22:10
 */
use yii\helpers\Url;
use yii\widgets\MaskedInput;

use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use app\helpers\Html;
use app\entities\EntityQuery;
use app\entities\Vedomstvo;
use app\entities\AdresnyjObjekt;
use app\entities\Dolzhnost;
use app\enums\EtapObrazovaniya;
use app\widgets\SwitchingFields;
use app\enums2\Rol;

$this->title = 'Регистрация нового пользователя';

$form = ActiveForm::begin([
    'fieldConfig' => [
        'template' => "{label}\n<div class=\"col-md-8\">\n{input}\n{hint}\n{error}\n</div>",
        'labelOptions' => ['class' => 'control-label col-md-4'],
    ]
]);

?>
<div class="row">
    <h3>Регистрация нового пользователя</h3>
    <div class="col-md-6 form-horizontal">
        <div class="fields-group-heading">
            <h3>Личные данные</h3>
        </div>

        <?= $form->field($model, 'familiya') ?>
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

        <div class="fields-group-heading">
            <h3>Работа</h3>
        </div>

        <?= $form->field($model, 'rabotaOrgVedomstvo')->widget(Select2::classname(), [
            'data' => Vedomstvo::find()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
        ]) ?>

        <?= $form->field($model, 'rabotaOrgAdres')->widget(Select2::classname(), [
            'data' => AdresnyjObjekt::findBurRajon()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'formalnoeNazvanie')
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
                'attribute' => 'rabotaOrgId',
                'switchIntroText' => 'Не нашли в списке?',
                'switchLinkText' => 'Нажмите здесь чтобы ввести организацию вручную.',

                'widgetClass' => DepDrop::classname(),
                'widgetConfig' => [
                    'type' => DepDrop::TYPE_SELECT2,
                    'data' => [$model->rabotaOrgId => null],
                    'pluginOptions'=>[
                        'depends' => [
                            Html::getInputId($model, 'rabotaOrgVedomstvo'),
                            Html::getInputId($model, 'rabotaOrgAdres')
                        ],
                        'loadingText' => 'Загрузка организаций...',
                        'initialize' => true,
                        'placeholder' => 'Выберите образовательную организацию',
                        'url' => Url::to(['kadry/rabota-org']),
                    ]
                ],
                'widgetConfigDisabled' => ['disabled' => true]
            ],
            'field2Options' => [
                'attribute' => 'rabotaOrgNazvanie',
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
                'switchIntroText' => 'Возможно ваша должность есть в списке.',
                'switchLinkText' => 'Нажмите здесь чтобы найти её в списке.'
            ]
        ]) ?>

        <?= $form->field($model, 'rabotaEtapObrazovaniya')->widget(Select2::className(), [
            'data' => EtapObrazovaniya::namesMap()
        ]) ?>

        <?= $form->field($model, 'rabotaTelefon')->widget(MaskedInput::className(), ['mask' => '+79999999999']) ?>

        <div class="fields-group-heading">
            <h3>Роли  пользователя в системе</h3>
        </div>

        <?= $form->field($model, 'roli')->widget(Select2::classname(), [
            'data' => Rol::names(),
            'options' => ['multiple' => true, 'placeholder' => 'Выберите роль ...'],
        ]) ?>

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
