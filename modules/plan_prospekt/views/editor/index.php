<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveFormAsset;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\grid\GridViewAsset;

use kartik\date\DatePickerAsset;
use kartik\select2\Select2;
use kartik\touchspin\TouchSpinAsset;

use app\modules\plan_prospekt\Asset;

/**
 * @var $this View
 * @var $actionSubview string
 * @var $actionParams array
 * @var $gridParams array
 */

ActiveFormAsset::register($this);
TouchSpinAsset::register($this);
DatePickerAsset::register($this);
GridViewAsset::register($this);

Asset::register($this);

echo Html::beginTag('div', ['class' => 'planprospekt planprospekt-editor']);

echo Html::tag('h3', 'План проспект ' . Yii::$app->request->get('year'));

// workaround for kratik-select2 pjax loading bug
echo Html::tag('div', Select2::widget(['name' => 'stub']), ['class' => 'hidden']);

Modal::begin([
    'id' => 'modal-action',
    'header' => '<h4></h4>',
    'options' => ['tabindex' => false]
]);

$this->registerJs('mybriop.planProspektEditor.modalDynamicOptionsInit("#modal-action");');

Pjax::begin(['id' => 'pjax-action', 'timeout' => 3500]);

if (isset($actionSubview) && isset($actionParams)) {
    $indexUrl = $actionParams['indexUrl'];
    $modalMethod = ($actionParams['model'] !== null) ? "show" : "hide";

    $this->registerJs('mybriop.planProspektEditor.modalHiddenHandlerInit("#modal-action", "#pjax-grid", "' . $indexUrl . '");');
    $this->registerJs('$("#modal-action").modal("' . $modalMethod . '");');

    echo $this->render($actionSubview, $actionParams);
}

Pjax::end();

Modal::end();

Pjax::begin(['id' => 'pjax-grid', 'timeout' => 3500]);

if (isset($gridParams)) {
    $this->registerJs('mybriop.planProspektEditor.gridActionButtonsInit(".btn-action", "#pjax-action");');

    echo $this->render('_grid', $gridParams);
}

// because pjax reload if response is empty
echo Html::tag('span', '', ['class' => 'hidden']);

Pjax::end();

echo Html::endTag('div');

echo Html::tag('div', '', ['id' => 'pjax-loading', 'class' => 'loader', 'style' => 'display:none']);
$this->registerJs('mybriop.planProspektEditor.pjaxLoadingIndicatorInit(window.document);');
