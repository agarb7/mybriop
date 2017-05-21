<?php
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title = 'Редактор типа должности "Учитель"';

$form = ActiveForm::begin();
?>
<div class="col-md-6">
<?=GridView::widget([
    'dataProvider' => $provider,
    'caption' => 'Должности общего справочника, относящиеся к категории "Учитель"',
    'showHeader' => false,
    'columns' => [
        'nazvanie',
        [
            'class' => 'yii\grid\CheckboxColumn',
            'checkboxOptions' => function ($provider){
                return ($provider->tip == 'uchprep')?['checked' => 'checked']:[];
            }
        ]
    ]
]);?>
<?=Html::submitButton('Сохранить',['class' => 'btn btn-primary']);?>
</div>
<?ActiveForm::end();?>
