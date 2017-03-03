<?php
use yii\data\ActiveDataProvider;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $commonData ActiveDataProvider
 * @var $privateData ActiveDataProvider
 * @var $this View
 */
$this->title = 'БРИОП - Редактор справочника должностей'
?>
<div class="row">
    <div class="col-md-6">
        <?= $this->render('_grid', [
            'id' => 'grid-common-dolzhnosti',
            'data' => $commonData,
            'tableCaption' => 'Общий справочник',
            'action' => ['merge'],
            'actionCaption' => 'Объеденить / переименовать'
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $this->render('_grid', [
            'id' => 'grid-private-dolzhnosti',
            'data' => $privateData,
            'tableCaption' => 'Ввод пользователей',
            'action' => ['move'],
            'actionCaption' => 'Объединить / сделать общим / переименовать'
        ]) ?>
    </div>
</div>
