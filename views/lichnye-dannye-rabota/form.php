<?php
use app\entities\EntityQuery;
use app\entities\Organizaciya;
use app\enums\OrgTipRaboty;
use app\models\lichnye_dannye_rabota\RabotaForm;
use app\widgets\ComboWidget;
use app\widgets\TelefonInput;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $model RabotaForm
 */
?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']) ?>

<?= $form->field($model, 'organizaciyaDir')->widget(ComboWidget::className(), [
    'data' => Organizaciya::find()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
]) ?>

<?= $form->field($model, 'org_tip')->dropDownList(OrgTipRaboty::namesMap()) ?>

<?= $form->field($model, 'telefon')->widget(TelefonInput::className()) ?>

<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
