<?php

$this->title = 'Экспертно-профильные группы';

$this->registerJsFile('/js/attestacionnayaKomissiya.js');
$this->registerJsFile('/js/angular.min.js');
$this->registerCssFile('css/attestacionnayaKomissiya.css',['depends' => [\app\assets\AppAsset::className()]]);

echo '<h2>Экспертно-профильные группы</h2><p>&nbsp;</p>';

?>


<div ng-app="komissii" class="komissii-content hidden" id="komissii">

    <div class="col-md-6"  ng-controller="KomissiiListController as komissii">

        <form ng-submit="komissii.addKomissiyu()" class="form-inline">
            <input type="text" ng-model="komissii.newNazvanie"  size="25"
                   placeholder="Название новой комиссии" class="form-control">
            <input class="btn btn-primary" type="submit" value="Добавить">
        </form>
        <br>
        <table class="att-tb" style="width: 100%">
            <tr class="thead">
                <td style="width: 70%">Название</td>
                <td></td>
            </tr>
            <tr ng-class="item.is_selected ? 'selected_komissiya' : ''" ng-repeat="item in komissii.list | orderBy:nazvanie">
                <td>
                    <span ng-hide="item.is_edit" class="komissiya{{item.id}}">{{item.nazvanie}}</span>
                    <div  ng-show="item.is_edit" class="form-inline">
                        <input style="width: 100%" class="form-control" ng-model="item.nazvanie_copy" id="input_nazvanie{{item.id}}" value="" type="text">
                    </div>

                </td>
                <td class="center">
                    <button title="Редактировать" data-toggle="tooltip"  ng-hide="item.is_edit" type="button" ng-click="komissii.editKomissiyu(item);" class="btn btn-default tool-btn" aria-label="Left Align">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </button>

                    <button title="Удалить" ng-hide="item.is_edit" type="button" class="btn btn-default tool-btn" ng-click="komissii.deleteKomissiyu(item);" aria-label="Left Align">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                    </button>

                    <button title="Должности" ng-hide="item.is_edit" type="button" class="btn btn-default tool-btn" ng-click="komissii.editDolzhnosti(item);" aria-label="Left Align">
                        <span class="glyphicon" style="font-weight: bold" aria-hidden="true">Д</span>
                    </button>

                    <button title="Состав комиссии" ng-hide="item.is_edit" type="button" class="btn btn-default tool-btn" ng-click="komissii.editRabotnikov(item);" aria-label="Left Align">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                    </button>

                    <button title="Сохранить изменения" ng-show="item.is_edit" type="button" class="btn btn-default tool-btn" ng-click="komissii.commitChanges(item);">
                        <span class="glyphicon glyphicon-ok-circle"></span>
                    </button>

                    <button title="Отменить изменения" ng-show="item.is_edit" type="button" class="btn btn-default tool-btn" ng-click="komissii.banChanges(item);">
                        <span class="glyphicon glyphicon-ban-circle"></span>
                    </button>
                </td>
            </tr>
        </table>

    </div>
    <div ng-controller="DolzhnostiListController as dolzhnosti" class="col-md-6" ng-show="dolzhnosti.selected_komissiya.id">
        <form ng-submit="dolzhnosti.addDolzhnost()" class="form-inline">
            <div class="inline-block valign-middle" style="width:80%">
                <?=kartik\select2\Select2::widget([
                    'name' => 'dolzhnosti',
                    'data' => \app\entities\Dolzhnost::find()->orderBy('nazvanie')->where('obschij')
                        ->formattedAll(\app\entities\EntityQuery::DROP_DOWN,'nazvanie'),
                    'options' => [
                        'placeholder' => 'Выберите должность',
                        'class' => 'form-control',
                        'ng-model' => 'dolzhnosti.dolzhnost',
                        'id' => 'dolzhnosti_select'
                    ],
                ])?>
            </div>
            <button class="btn btn-primary">Добавить</button>
        </form>
        <br>
        <table class="att-tb" style="width: 100%">
            <tr class="thead">
                <td style="width: 80%">Название</td>
                <td></td>
            </tr>
            <tr ng-repeat="item in dolzhnosti.list | orderBy:nazvanie">
                <td>
                    <span class="rabotnik{{item.id}}">{{item.nazvanie}}</span>
                </td>
                <td class="center">
                    <button title="Удалить"  type="button" class="btn btn-default tool-btn" ng-click="dolzhnosti.deleteDolzhnost(item);" aria-label="Left Align">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                    </button>
                </td>
            </tr>
        </table>
    </div>

    <div class="col-md-6" ng-show="rabotniki.selected_komissiya.id" ng-controller="RabotnikiListController as rabotniki">
        <form ng-submit="rabotniki.addRabotnika()" class="form-inline">
            <div class="inline-block valign-middle" style="width:80%">
                <?
                $url = \yii\helpers\Url::to(['fiz-lico-list']);
                ?>
                <?=kartik\select2\Select2::widget([
                    'name' => 'rabotniki',
                    'options' => [
                        'placeholder' => 'Введите фамилию работника',
                        'ng-model' => 'rabotniki.rabotnik',
                        'id'=>'rabotnik'
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 3,
                        'ajax' => [
                            'url' => $url,
                            'dataType' => 'json',
                            'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }')
                        ],
                        'escapeMarkup' => new \yii\web\JsExpression('function (markup) { return markup; }'),
                        'templateResult' => new \yii\web\JsExpression('function(fiz_lico) { return fiz_lico.text; }'),
                        'templateSelection' => new \yii\web\JsExpression('function (fiz_lico) { return fiz_lico.text; }'),
                    ],
                ])?>
            </div>
            <button class="btn btn-primary">Добавить</button>
        </form>
        <br>
        <table class="att-tb" style="width: 100%">
            <tr class="thead">
                <td style="width: 80%">Название</td>
                <td class="center">Председатель</td>
                <td></td>
            </tr>
            <tr ng-repeat="item in rabotniki.list | orderBy:['familiya','imya','otchestvo']">
                <td>
                    <span class="rabotniki{{item.id}}">{{item.fizLicoRel.familiya}} {{item.fizLicoRel.imya}} {{item.fizLicoRel.otchestvo}}</span>
                </td>
                <td class="center">
                    <input type="checkbox" ng-model="item.predsedatel" ng-change="rabotniki.setPredsedatel(item);">
                </td>
                <td class="center">
                    <button title="Удалить"  type="button" class="btn btn-default tool-btn" ng-click="rabotniki.deleteRabotnika(item);" aria-label="Left Align">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                    </button>
                </td>
            </tr>
        </table>
    </div>

</div>



