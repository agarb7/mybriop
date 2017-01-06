<?php

use \app\helpers\Html;
$this->title = 'Оценивание аттестующихся';

$this->registerJsFile('/js/angular.min.js');
$this->registerJsFile('/js/angular-sanitize.min.js');
$this->registerJsFile('/js/sotrudnikAttKomissii.js');
$this->registerCss('.otsenki-tb{min-width:800px} .filter-block{margin-right:0.5em}');


$periods_for_dropdown = [];
foreach ($periods as $period) {
    if ($period['nachalo'] > '2016-08-30')
        $periods_for_dropdown[$period['id']] = 'с '.\Yii::$app->formatter->asDate($period['nachalo'],'php:d.m.Y').
            ' по '.\Yii::$app->formatter->asDate($period['konec'],'php:d.m.Y');
}

?>
<div ng-app="otsenki">

    <div  ng-show="s.is_show" class="inline-block" ng-controller="SpisokController as s">
        <div ng-show="s.hide_zayvlenie">
            <div class="inline-block filter-block">
                <?=Html::label('Период прохождения аттестации','periods',[]);?>
                <?=Html::dropDownList('periods',null,$periods_for_dropdown,[
                    'id'=>'periods',
                    'class'=>'form-control inline-block',
                    'ng-model'=>'s.period',
                    'ng-disabled'=>'s.allUnfinished'
                ]);?>
            </div>
            <div class="inline-block checkbox filter-block">
                <label for="all_unfinished">
                    <input type="checkbox" id="all_unfinished" ng-change="s.toggleUnfinished()" ng-model="s.allUnfinished"/>
                    Все необработанные
                </label>
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
                         <td>{{item.organizaciya_nazvanie+', '+item.dolzhnost_nazvanie}}</td>
                         <td>
                             <button class="btn btn-primary btn-block" ng-click="s.putMarks(item.id)">Поставить оценки</button>
                             <button class="btn btn-primary btn-block" ng-click="s.getZayavlenie(item.id)">Заявление</button>
                         </td>
                     </tr>
                </table>
            </div>
        </div>

        <div ng-show="!s.hide_zayvlenie">
            <div class="row">
                <button type="button" class="btn btn-primary" ng-click="s.backToList()">Назад</button>
            </div>
            <div class="row" ng-bind-html="s.currentZayavlenieContent">
                {{ s.currentZayavlenieContent }}
            </div>
        </div>
    </div>



    <div ng-show="o.is_show" ng-controller="OtsenkiController as o">

        <button class="btn btn-primary" ng-click="o.back()">Назад</button>

        <div ng-repeat="list in o.lists">
            <h3>Оценочный лист для "{{list.ispytanieName}}"</h3>
            <p>Загруженный файл: <a target="_blank" href="{{list.fileLink}}" class="file_item">{{list.fileName}}</a></p>
            <p>
                <span ng-show="list.minBallPervayaKategoriya" class="bold">Проходной балл 1 категория: {{list.minBallPervayaKategoriya}}; </span>
                <span ng-show="list.minBallVisshayaKategoriya" class="bold">Проходной балл высшая категория: {{list.minBallVisshayaKategoriya}}</span>
            </p>
            <table class="tb otsenki-tb">
                <tr class="thead">
                    <td>№</td>
                    <td>Показатели для оценки</td>
                    <td class="center">Максимальное значение</td>
                    <td class="center">Оценка</td>
                </tr>
                <tr ng-repeat="strukturaItem in list.struktura" ng-class="(strukturaItem.uroven == 1 && !areThereChildren(list.struktura, strukturaItem)  ? 'bold' : '')">
                    <td>{{strukturaItem.nomer}}</td>
                    <td>{{strukturaItem.nazvanie}}</td>
                    <td class="center">{{strukturaItem.max_bally}}</td>
                    <td class="center">
                        <span ng-if="list.status == 'zapolneno' || o.areThereChildren(list.struktura, strukturaItem)">{{strukturaItem.bally}}</span>
                        <input ng-if="list.status == 'redaktiruetsya' && !o.areThereChildren(list.struktura, strukturaItem)" class="form-control block-center"
                               ng-change="o.changeMark(list.struktura, strukturaItem)" ng-model="strukturaItem.bally"
                               type="number" min="0" style="width:5em">
                    </td>
                </tr>
                <tr class="footer">
                    <td colspan="2" class="text-right">Итого</td>
                    <td class="center">{{o.calculateMaxSumm(list)}}</td>
                    <td class="center">{{o.calculateSumm(list)}}</td>
                </tr>
            </table>
            <p>
                <br>
                <button ng-click="o.saveOtsenki(list)" class="btn btn-primary">Сохранить</button>
            </p>
        </div>

    </div>

</div>