<?php
    use \yii\helpers\Html;

    $this->title = 'Обработка заявлений';

    $this->registerJsFile('js/rukovoditelKomissii.js');
    $this->registerJsFile('js/angular.min.js');

    $periods_for_dropdown = [];// array_map(function($item){return $item['nachalo'].'-'.$item['konec'];},$periods);
    foreach ($periods as $period) {
        $periods_for_dropdown[$period['id']] = 'с '.\Yii::$app->formatter->asDate($period['nachalo'],'php:d.m.Y').
                                               ' по '.\Yii::$app->formatter->asDate($period['konec'],'php:d.m.Y');
    }
?>

<div ng-app="rukovoditel" ng-controller="RukovoditelKomissiiController as rk">
    <div class="inline-block">
        <div class="inline-block">
            <?=Html::label('Период прохождения аттестации','periods',[]);?>
            <?=Html::dropDownList('periods',null,$periods_for_dropdown,['id'=>'periods','class'=>'form-control inline-block']);?>
        </div>
        <div class="inline-block relative" style="top: -1px">
            <?=Html::button('Загрузить список заявлений',['class'=>'btn btn-primary','ng-click'=>'rk.loadZayavleniya()'])?>
        </div>
    </div>
    <div ng-show="rk.is_show_table">
        <h3>Список заявлений</h3>
        <button class="btn btn-primary" ng-click="rk.saveChanges()">Сохранить изменения</button>
        <table class="table">
            <thead>
                <tr class="active">
                    <td></td>
                    <td ng-repeat="rabotnik in rk.rabotniki" class="center">
                        {{rabotnik.familiya+' '+rabotnik.imya+' '+rabotnik.otchestvo}}
                    </td>
                </tr>
            </thead>
            <tr>
                <td>
                    <button class="btn btn-default"  ng-click="rk.checkAll()">Проставить для всех</button>
                </td>
                <td ng-repeat="(key,rabotnik) in rk.rabotniki | orderBy:'familiya'" class="center">
                    <input type="checkbox" ng-model="rabotnik.checked">
                </td>
            </tr>
            <tr ng-show="rk.zayavleniya" ng-repeat="zayavlenie in rk.zayavleniya | orderBy:['-raspredelenieCopy.lenght','familiya']" ng-class="zayavlenie.raspredelenieCopy.length > 0 ? 'success' : ''">
                <td>{{zayavlenie.familiya+' '+zayavlenie.imya+' '+zayavlenie.otchestvo}}</td>
                <td ng-repeat="rabotnik in rk.rabotniki" class="center valign-middle">
                    <input type="checkbox" ng-click="rk.checkOne(zayavlenie,rabotnik.rabotnikId)" ng-checked="zayavlenie.raspredelenieCopy.indexOf(rabotnik.rabotnikId) > -1">
                </td>
            </tr>
        </table>
    </div>
</div>
