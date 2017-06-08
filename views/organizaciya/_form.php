<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\entities\AdresnyjObjekt;
use app\entities\EntityQuery;
use app\enums\EtapObrazovaniya;
use app\models\vedomstvo\Vedomstvo;
use app\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\organizaciya\Organizaciya */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organizaciya-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nazvanie')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'adres_adresnyj_objekt')->widget(Select2::className(),[
        'data' => AdresnyjObjekt::findBurRajon()->commonOnly()->formattedAll(EntityQuery::DROP_DOWN, 'oficialnoeNazvanie'),
        'options' => ['placeholder' => 'Выберите район',],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <?= $form->field($model, 'etapy_obrazovaniya')->widget(Select2::className(),[
        'data' => EtapObrazovaniya::namesMap(),
        'options' => [
            'multiple' => true,
        ]
    ]); ?>

    <?= $form->field($model, 'obschij')->checkbox() ?>

    <?= $form->field($model, 'vedomstvo')->widget(Select2::className(),[
        'data' => ArrayHelper::map(Vedomstvo::find()->all(), 'id', 'nazvanie'),
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
