<?php
use yii\helpers\Html;
use \app\entities\VremyaProvedeniyaAttestacii;
use app\modules\attestaciya_otchety\Asset;

Asset::register($this);

$this->title = 'Отчет по ИК';
?>

<div class="row relative">
    <div class="col-md-4">
        <?=Html::label('Период прохождения аттестации','periods',[]);?>
        <?=Html::dropDownList('periods',null,VremyaProvedeniyaAttestacii::getItemsToSelect(),
            [
                'id'=>'periods',
                'prompt' => 'Выберите период',
                'class'=>'form-control inline-block',
                'onchange'=>'$.post( "'.Yii::$app->urlManager->createUrl('attestaciya-otchety/list/spisok-attestuemyh?vp=').'"+$(this).val(), function( data ) {
                  $( "select#spisok" ).html( data );
                });'
            ]);?>
    </div>

    <div class="col-md-4">
        <?=Html::label('Список аттестуемых','spisok',[]);?>
        <select id="spisok" onchange="change_url()">
            <option>Сначала нужно выбрать период</option>
        </select>
    </div>

    <div class="col-md-4" style="position:absolute;bottom:0;right: 0">
        <?=Html::a('Загрузить отчет','',['id'=>'report_btn','target'=>'_blank','class'=>'btn btn-primary bottom'])?>
    </div>
</div>

