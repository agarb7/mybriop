<?php
use app\entities\Dolzhnost;
use app\entities\EntityQuery;
use app\enums\EtapObrazovaniya;
use app\enums\OrgTipDolzhnosti;
use app\models\lichnye_dannye_dolzhnost\DolzhnostForm;
use app\widgets\ComboWidget;
use app\widgets\TouchSpin;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var $model DolzhnostForm
 */
?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']) ?>

<?= $form->field($model, 'dolzhnostDir')->widget(ComboWidget::className(), [
    'data' => Dolzhnost::find()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'nazvanie')
]) ?>

<?= $form->field($model, 'org_tip')->dropDownList(OrgTipDolzhnosti::namesMap()) ?>

<?= $form->field($model, 'etap_obrazovaniya')->dropDownList(EtapObrazovaniya::namesMap()) ?>

<?= $form->field($model, 'stazh')->widget(TouchSpin::className()) ?>

<?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
