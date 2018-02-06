<?php
use yii\helpers\Html;
use \app\entities\VremyaProvedeniyaAttestacii;
use app\modules\attestaciya_otchety\DolzhnostiAsset;

DolzhnostiAsset::register($this);

$this->title = 'Отчет по нераспределенным должностям';

$this->registerJsFile('/js/angular.min.js');
?>
<div ng-app="loss" ng-controller="LossController as l">


    <div class="row relative" >
        <div class="col-md-6">
            <?=Html::label('Период прохождения аттестации','periods',[]);?>
            <?=Html::dropDownList('periods',null,VremyaProvedeniyaAttestacii::getItemsToSelect(),['ng-model' => 'l.vp','id'=>'periods','class'=>'form-control inline-block']);?>
        </div>
        <div class="col-md-6" style="position:absolute;bottom:0;right: 0">
            <button class="btn btn-primary" ng-click="l.loss()">Загрузить</button>
        </div>
    </div>
    <p>&nbsp;</p>
    <div class="row">

        <div class="col-md-12">
            <table class="tb">
                <thead>
                    <tr class="thead">
                        <td>№</td>
                        <td>ФИО</td>
                        <td>Должность</td>
                    </tr>
                </thead>
                <tbody>
                <tr ng-repeat="dolzhnost in l.dolzhnosti">
                    <td>{{ dolzhnost.id }}</td>
                    <td>{{ dolzhnost.fio }}</td>
                    <td>{{ dolzhnost.dolzhnost }}</td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>


</div>