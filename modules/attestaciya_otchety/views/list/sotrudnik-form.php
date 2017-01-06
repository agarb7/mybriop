<?php
use yii\helpers\Html;
use \app\entities\VremyaProvedeniyaAttestacii;
use app\modules\attestaciya_otchety\Asset;
use \app\entities\AttestacionnayaKomissiya;
use\app\modules\attestaciya_otchety\SotrudnikiAsset;

SotrudnikiAsset::register($this);

$this->title = 'Отчет по сотруднику';

$this->registerJsFile('/js/angular.min.js');
?>
<div ng-app="othect" ng-controller="OtchetController as o">


    <div class="row relative" >
        <div class="col-md-4">
            <?=Html::label('Период прохождения аттестации','periods',[]);?>
            <?=Html::dropDownList('periods',null,VremyaProvedeniyaAttestacii::getItemsToSelectFromSeptember(),['ng-model' => 'o.vp','id'=>'periods','class'=>'form-control inline-block']);?>
        </div>
        <div class="col-md-4" >
            <?=Html::label('Период прохождения аттестации','komissiya',[]);?>
            <?=Html::dropDownList('komissiya',null,AttestacionnayaKomissiya::getKomissiiForDropDown(),['ng-model' => 'o.komissiya', 'id'=>'komissiya','class'=>'form-control inline-block']);?>
        </div>
        <div class="col-md-4" style="position:absolute;bottom:0;right: 0">
            <button class="btn btn-primary" ng-click="o.load()">Загрузить</button>
        </div>
    </div>
    <p>&nbsp;</p>
    <div class="row">

        <div class="col-md-12">
            <table class="tb">
                <thead>
                    <tr class="thead">
                        <td>ФИО</td>
                        <td>Количество</td>
                    </tr>
                </thead>
                <tbody>
                <tr ng-repeat="sotrudnik in o.sotrudniki">
                    <td>{{ sotrudnik.familiya }} {{ sotrudnik.imya }} {{ sotrudnik.othestvo }}</td>
                    <td>{{ sotrudnik.count }}</td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>


</div>