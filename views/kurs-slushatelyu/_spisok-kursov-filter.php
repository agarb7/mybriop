<?php

use app\entities\EntityQuery;
use app\entities\FizLico;
use app\entities\KategoriyaSlushatelya;
use app\models\kurs_slushatelyu\SpisokKursovFilterForm;
use app\widgets\DatePicker;
use app\widgets\TouchSpin;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var SpisokKursovFilterForm $model
 * @var View $this
 */

$js = <<<'JS'
$('.spisok-kursov-filter').each(function() {
    var $this = $(this);
    var $form_cont = $this.children('.form-container');
    $this.children('.switch-container').children('.switch').click(function () {
        $form_cont.toggle();

        return false;
    });

    $form_cont.find('.reset-btn').click(function () {
        $this.hide();

        var $form = $form_cont.find('form');
        $form.find(':input').remove();
        $form.submit();

        return false;
    });
});
JS;

$this->registerJs($js);

function hasFilter()
{
    foreach (Yii::$app->request->get() as $k=>$v) {
        if ($k !== 'page' && $v)
            return true;
    }

    return false;
}

?>

<div class="spisok-kursov-filter">
    <div class="switch-container"><a class="switch" href="#">Фильтры</a></div>

    <?= Html::beginTag('div', [
        'class' => 'form-container',
        'style' => hasFilter() ? null : 'display:none'
    ]) ?>
        <?php
        $form = ActiveForm::begin([
            'method' => 'get',
            'action' => [Yii::$app->controller->id . '/' . Yii::$app->controller->action->id]
        ]) ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'kategoriiSlushatelej')->widget(Select2::className(), [
                    'data' => KategoriyaSlushatelya::find()->formattedAll(EntityQuery::CHECKBOX_LIST, 'nazvanie'),
                    'options' => ['placeholder' => '',  'multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>

            <div class="col-md-8">
                <?= $form->field($model, 'nazvanie') ?>

                <?= $form->field($model, 'rukovoditel')->widget(Select2::className(), [
                    'data' => FizLico::findRukovoditeliKursov()->formattedAll(EntityQuery::DROP_DOWN, 'familiyaInicialy'),
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>

                <?= $form->field($model, 'chasy')->widget(TouchSpin::className()) ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'nachalo')->widget(DatePicker::className()) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'konec')->widget(DatePicker::className()) ?>
                    </div>
                </div>
            </div>
        <?= Html::endTag('div') ?>

        <?= Html::submitButton('Применить фильтры', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Сбросить фильтры', ['class' => 'btn reset-btn']) ?>

        <?php ActiveForm::end() ?>
    </div>
</div>