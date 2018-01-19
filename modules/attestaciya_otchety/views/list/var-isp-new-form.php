<?php
use yii\helpers\Html;
use \app\entities\VremyaProvedeniyaAttestacii;
use app\modules\attestaciya_otchety\Asset;

Asset::register($this);
$this->title = 'Итоговый отчет';
?>
<div class="row relative">
    <div class="col-md-6">
        <?=Html::label('Период прохождения аттестации','periods',[]);?>
        <?=Html::dropDownList('periods',null,VremyaProvedeniyaAttestacii::getItemsToSelect(),['id'=>'periods','class'=>'form-control inline-block', 'onchange'=>'change_url()']);?>
    </div>
    <div class="col-md-6" style="position:absolute;bottom:0;right: 0">
        <?=Html::a('Загрузить отчет','',['id'=>'report_btn','target'=>'_blank','class'=>'btn btn-primary bottom'])?>
    </div>
</div>