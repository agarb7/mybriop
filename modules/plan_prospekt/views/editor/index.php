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

use app\modules\plan_prospekt\EditorAsset;

/**
 * @var $this View
 * @var $actionSubview string
 * @var $actionParams array
 * @var $gridParams array
 */

// workaround for kratik-select2 pjax loading bug
echo Html::tag('div', Select2::widget(['name' => 'stub']), ['class' => 'hidden']);

ActiveFormAsset::register($this);
TouchSpinAsset::register($this);
DatePickerAsset::register($this);
GridViewAsset::register($this);

EditorAsset::register($this);

Modal::begin([
    'id' => 'modal-action',
    'header' => '<h4>666</h4>'
]);

Pjax::begin(['id' => 'pjax-action']);

if (isset($actionSubview) && isset($actionParams)) {
    $indexUrl = $actionParams['indexUrl'];
    $modalMethod = ($actionParams['model'] !== null) ? "show" : "hide";

    $this->registerJs('mybriop.planProspektEditor.modalHiddenHandlerInit("#modal-action", "#pjax-grid", "' . $indexUrl . '");');
    $this->registerJs('$("#modal-action").modal("' . $modalMethod . '");');

    echo $this->render($actionSubview, $actionParams);
}

Pjax::end();

Modal::end();

Pjax::begin(['id' => 'pjax-grid']);

if (isset($gridParams)) {
    $this->registerJs('mybriop.planProspektEditor.gridActionButtonsInit(".btn-action", "#pjax-action");');

    echo $this->render('_grid', $gridParams);
}

Pjax::end();
