<?php
use \app\helpers\Html;
$this->title = 'Список заявлений';

$this->registerJsFile('/js/angular.min.js');
$this->registerJsFile('/js/municipalnyjOtvetstvennyjList.js');

$periods_for_dropdown = [];
foreach ($periods as $period) {
    if ($period['nachalo'] > '2016-08-30')
        $periods_for_dropdown[$period['id']] = 'с '.\Yii::$app->formatter->asDate($period['nachalo'],'php:d.m.Y').
            ' по '.\Yii::$app->formatter->asDate($period['konec'],'php:d.m.Y');
}

?>

<div ng-app="list" ng-controller="MOListController as mol">
    <div class="row">
        <div class="inline-block filter-block">
            <?=Html::label('Период прохождения аттестации','periods',[]);?>
            <?=Html::dropDownList('periods',null,$periods_for_dropdown,[
                'id'=>'periods',
                'class'=>'form-control inline-block',
                'ng-model'=>'mol.period',
                //'ng-disabled'=>'s.allUnfinished'
            ]);?>
        </div>
        <div class="inline-block relative" style="top: -1px">
            <?=Html::button('Загрузить список заявлений',['class'=>'btn btn-primary','ng-click'=>'mol.loadZayavleniya()'])?>
        </div>
    </div>
    <p></p>
    <div class="row">
        <table class="tb" ng-show="mol.list.length > 0">
            <tr class="thead">
                <td style="width: 300px">ФИО</td>
                <td style="width:400px;">Школа</td>
                <td>Портфолио</td>
            </tr>
            <tr ng-repeat="zayavlenie in mol.list">
                <td>{{ zayavlenie.familiya }} {{ zayavlenie.imya }} {{ zayavlenie.otchestvo }}</td>
                <td>{{ zayavlenie.organizaciyaRel.nazvanie }}</td>
                <td class="center">
                    <i class="fa fa-check" ng-show="zayavlenie.portfolio"></i>
                    <i class="fa fa-minus" ng-show="!zayavlenie.portfolio"></i>
                </td>
            </tr>
        </table>
    </div>
</div>
