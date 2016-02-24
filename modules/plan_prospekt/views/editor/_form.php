<?php
use app\enums2\FormaObucheniya;
use app\enums2\TipFinansirovaniya;
use app\enums2\TipKursa;
use app\modules\plan_prospekt\models\KursForm;
use app\records\FizLico;
use app\records\KategoriyaSlushatelya;
use app\records\Organizaciya;
use app\widgets\DatePicker;
use app\widgets\TouchSpin;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $this View
 * @var $model KursForm
 */

?>

<?php if (isset($model)): ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true]]) ?>

    <?= $form->field($model, 'tip')->widget(Select2::className(), [
        'data' => TipKursa::names(),
        'options' => ['placeholder' => ''],
        'hideSearch' => true,
        'pluginOptions' => ['allowClear' => true],
    ]) ?>

    <?= $form->field($model, 'nazvanie')->textInput() ?>

    <?= $form->field($model, 'annotaciya')->textarea() ?>

    <?= $form->field($model, 'kategorii_slushatelej')->widget(Select2::className(), [
        'data' => ArrayHelper::map(KategoriyaSlushatelya::find()->asArray()->all(), 'id', 'nazvanie'),
        'options' => ['placeholder' => '',  'multiple' => true],
        'pluginOptions' => ['allowClear' => true],
    ]) ?>

    <?= $form->field($model, 'formy_obucheniya_widget')->widget(Select2::className(), [
        'data' => FormaObucheniya::names(),
        'options' => ['placeholder' => '',  'multiple' => true],
        'pluginOptions' => ['allowClear' => true],
        'hideSearch' => true,
    ]) ?>

    <?= $form->field($model, 'raschitano_chasov')->widget(TouchSpin::className()) ?>

    <?= $form->field($model, 'ochnoe_nachalo')->widget(DatePicker::className()) ?>
    <?= $form->field($model, 'ochnoe_konec')->widget(DatePicker::className()) ?>
    <?= $form->field($model, 'zaochnoe_nachalo')->widget(DatePicker::className()) ?>
    <?= $form->field($model, 'zaochnoe_konec')->widget(DatePicker::className()) ?>

    <?= $form->field($model, 'raschitano_slushatelej')->widget(TouchSpin::className()) ?>

    <?= $form->field($model, 'finansirovanie')->widget(Select2::className(), [
        'data' => TipFinansirovaniya::names(),
        'options' => ['placeholder' => ''],
        'hideSearch' => true,
        'pluginOptions' => ['allowClear' => true],
    ]) ?>

    <?= $form->field($model, 'rukovoditel')->widget(Select2::className(), [
        'data' => ArrayHelper::map(
            FizLico::find()
                ->joinWith('raboty_fiz_lica_rel')
                ->where(['rabota_fiz_lica.organizaciya' => Organizaciya::ID_BRIOP])
                ->asArray()
                ->all(),
            'id',
            function ($fizLico) {
                return Yii::$app->formatter->asFizLico($fizLico);
            }
        ),
        'options' => ['placeholder' => ''],
        'pluginOptions' => ['allowClear' => true]
    ]) ?>

    <?= Html::submitButton() ?>

    <?php ActiveForm::end() ?>

<?php endif ?>

<?= $this->render('_data-tag', compact('model', 'backUrl')) ?>