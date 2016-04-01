<?php

use \app\helpers\Html;
$this->title = 'Оценивание аттестующихся';

$this->registerJsFile('/js/angular.min.js');
$this->registerJsFile('/js/sotrudnikAttKomissii.js');


$periods_for_dropdown = [];
foreach ($periods as $period) {
    $periods_for_dropdown[$period['id']] = 'с '.\Yii::$app->formatter->asDate($period['nachalo'],'php:d.m.Y').
        ' по '.\Yii::$app->formatter->asDate($period['konec'],'php:d.m.Y');
}

?>
<div ng-app="otsenki">

    <div class="inline-block" ng-controller="SpisokController as s">
        <div class="inline-block">
            <?=Html::label('Период прохождения аттестации','periods',[]);?>
            <?=Html::dropDownList('periods',null,$periods_for_dropdown,['id'=>'periods','class'=>'form-control inline-block','ng-model'=>'s.period']);?>
        </div>
        <div class="inline-block relative" style="top: -1px">
            <?=Html::button('Загрузить список заявлений',['class'=>'btn btn-primary','ng-click'=>'s.loadZayavleniya()'])?>
        </div>
        <p></p>
        <div>
            <table ng-class="s.spisok.length > 0 ? '' : 'hidden' " class="tb">
                <tr class="thead">
                    <td>ФИО</td>
                    <td>Должность</td>
                    <td></td>
                </tr>
                 <tr ng-repeat="item in s.spisok">
                     <td>{{item.familiya+' '+item.imya+' '+item.otchestvo}}</td>
                     <td>{{item.organizaciyaRel.nazvanie+', '+item.dolzhnostRel.nazvanie}}</td>
                     <td><button class="btn btn-primary" ng-click="s.putMarks(item.id)">Поставить оценки</button></td>
                 </tr>
            </table>
        </div>
    </div>

    <div ng-controller="OtsenkiController as o">

    </div>

</div>