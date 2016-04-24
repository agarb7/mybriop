<?php
    use \yii\helpers\Html;

    $this->title = 'Обработка заявлений';

    $this->registerJsFile('/js/rukovoditelKomissii.js');
    $this->registerJsFile('/js/angular.min.js');

    $periods_for_dropdown = [];// array_map(function($item){return $item['nachalo'].'-'.$item['konec'];},$periods);
    foreach ($periods as $period) {
        $periods_for_dropdown[$period['id']] = 'с '.\Yii::$app->formatter->asDate($period['nachalo'],'php:d.m.Y').
                                               ' по '.\Yii::$app->formatter->asDate($period['konec'],'php:d.m.Y');
    }

    $style = <<<STYLE
    .bally-bubble ul{
        margin:0;
        padding: 0 10px;
    }
    .bally-bubble{
        text-align: left;
        position: absolute;
        background: #fff;
        padding: 5px;
        border-radius: 0.25em;
        box-shadow: 0 0 10px #ddd;
        padding:5px 10px;
        min-width:240px;
        margin-top: 6px;
    }
    .bally-bubble:before{
        /*content: ' ';*/
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 12px solid #fff;
        position: absolute;
        top: -10px;
        left: 90px;

    }
    .zero-tr td{

    }

STYLE;

    $this->registerCss($style);


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
        <p><button class="btn btn-primary" ng-click="rk.saveChanges()">Сохранить изменения</button></p>
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
            <tr ng-show="rk.zayavleniya" ng-repeat-start="zayavlenie in rk.zayavleniya | orderBy:['-raspredelenieCopy.lenght','familiya']" ng-class="zayavlenie.raspredelenieCopy.length > 0 ? 'success' : ''">
                <td><span class="slink" ng-click="rk.showBally(zayavlenie.id)">{{zayavlenie.familiya+' '+zayavlenie.imya+' '+zayavlenie.otchestvo}}</span></td>
                <td ng-repeat="rabotnik in rk.rabotniki" class="center valign-middle">
                    <input type="checkbox" ng-click="rk.checkOne(zayavlenie,rabotnik.rabotnikId)" ng-checked="zayavlenie.raspredelenieCopy.indexOf(rabotnik.rabotnikId) > -1">
                    <span>{{rk.avgBall(rabotnik.fizLico, zayavlenie.otsenki)}}</span>
                </td>
            </tr>
            <tr ng-repeat-end="" class="hidden" id="otsenki_{{zayavlenie.id}}">
                <td colspan="{{(rk.objectLen(rk.rabotniki) + 1)}}">
                <div ng-repeat="(rabotnikId,list) in zayavlenie.otsenki">
                    <span>
                        {{rk.rabotniki[rabotnikId].familiya+' '+rk.rabotniki[rabotnikId].imya+' '+rk.rabotniki[rabotnikId].otchestvo}}
                    </span>
                    <span ng-click="rk.signOtsenki(zayavlenie.statuses[rabotnikId])" class="btn btn-primary btn-xs" ng-if="zayavlenie.statuses[rabotnikId].status == '<?=\app\enums2\StatusOtsenokZayavleniya::REDAKTIRUETSYA?>'">
                        Подписать
                    </span>
                    <ul>
                        <li ng-repeat="otsenka in list">
                            {{otsenka.nazvanie}} - {{otsenka.bally ? otsenka.bally : 0}}
                            <button ng-if="zayavlenie.statuses[rabotnikId].status == '<?=\app\enums2\StatusOtsenokZayavleniya::REDAKTIRUETSYA?>'" ng-click="rk.resetBally(otsenka)" class="btn btn-default btn-xs" title="Обнулить оценку">
                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                            </button>
                        </li>
                    </ul>
                </div>
                </td>
            </tr>
        </table>

    </div>
</div>
