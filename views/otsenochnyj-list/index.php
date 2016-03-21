<?php
$this->title = 'Оценочные листы';
$this->registerJsFile('/js/angular.min.js');
$this->registerJsFile('/js/otsenochnyeListy.js');
$this->registerCssFile('css/attestacionnayaKomissiya.css',['depends' => [\app\assets\AppAsset::className()]]);
?>

<div id="otsenochnye-listy" class="komissii-content">
    <div ng-controller="MainController as main" class="col-md-6">
        <div>
            <form ng-submit="main.addOtsenochnyjList()" class="">
                <div>
                    <div class="form-group col-md-4 no-padding">
                        <label>Название листа</label>
                        <input type="text" ng-model="main.newNazvanie"  size="25"
                               placeholder="Название нового листа" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label>1 кат.</label>
                        <input type="number" ng-model="main.minBallPervayKategoriya"
                               placeholder="1 кат." class="form-control">
                    </div>
                    <div class="form-group col-md-3 no-padding" style="padding-right: 15px !important;">
                        <label>высш. кат.</label>
                        <input type="number" ng-model="main.minBallVisshayaKategoriya"
                               placeholder="высш. кат." class="form-control">
                    </div>
                    <div class="form-group col-md-2 no-padding">
                        <label>&nbsp;</label>
                        <input style="font-size:0.9em" class="btn btn-primary form-control" type="submit" value="Добавить">
                    </div>
                </div>
            </form>
            <br>
            <table style="width:100%" class="att-tb">
                <tr class="thead">
                    <td style="width:70%">Название</td>
                    <td>&nbsp;</td>
                </tr>
                <tr ng-repeat="list in main.otsenochnyeListy" ng-class="list.is_selected ? 'selected_komissiya' : ''">
                    <td>
                        <div ng-hide="list.is_edit">
                            <span>{{list.nazvanie}}</span>
                            <div class="" style="font-size:0.9em">
                                <span ng-show="list.min_ball_pervaya_kategoriya != null">1 категория: {{list.min_ball_pervaya_kategoriya}} б.</span>
                                <span ng-show="list.min_ball_pervaya_kategoriya != null">&nbsp;</span>
                                <span ng-show="list.min_ball_visshaya_kategoriya != null">Высшая категория: {{list.min_ball_visshaya_kategoriya}} б.</span>
                            </div>
                        </div>
                        <div  ng-show="list.is_edit" class="form">
                            <div class="form-group col-md-6 no-padding">
                                <label>Название</label>
                                <input style="width: 100%" class="form-control" ng-model="list.nazvanie_copy" id="list-nazvanie{{list.id}}" value="" type="text">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="">1 кат.</label>
                                <input style="width: 100%" string-to-number class="form-control" ng-model="list.min_ball_pervaya_kategoriya_copy" value="" type="number">
                            </div>
                            <div class="form-group col-md-3  no-padding">
                                <label for="">высш. кат.</label>
                                <input style="width: 70%" string-to-number class="form-control" ng-model="list.min_ball_visshaya_kategoriya_copy" value="" type="number">
                            </div>
                        </div>

                    </td>
                    <td class="center">
                        <button title="Редактировать" data-toggle="tooltip"  ng-hide="list.is_edit" type="button" ng-click="main.editOtsenochnyjList(list);" class="btn btn-default tool-btn" aria-label="Left Align">
                            <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                        </button>

                        <button title="Удалить" ng-hide="list.is_edit" type="button" class="btn btn-default tool-btn" ng-click="main.deleteOtsenochnyjList(list);" aria-label="Left Align">
                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                        </button>

                        <button title="Редактировать стуктуру листа" ng-hide="list.is_edit" type="button" class="btn btn-default tool-btn" ng-click="main.editStruktura(list);" aria-label="Left Align">
                            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
                        </button>

                        <button title="Редактировать испытания листа" ng-hide="list.is_edit" type="button" class="btn btn-default tool-btn" ng-click="main.editIspytaniya(list);" aria-label="Left Align">
                            <span class="" aria-hidden="true">И</span>
                        </button>

                        <button title="Сохранить изменения" ng-show="list.is_edit" type="button" class="btn btn-default tool-btn" ng-click="main.commitChanges(list);">
                            <span class="glyphicon glyphicon-ok-circle"></span>
                        </button>

                        <button title="Отменить изменения" ng-show="list.is_edit" type="button" class="btn btn-default tool-btn" ng-click="main.cancelChanges(list);">
                            <span class="glyphicon glyphicon-ban-circle"></span>
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div ng-controller="StrukturaController as struktura" id="struktura" class="col-md-6" ng-show="struktura.selectedList != -1">
        <div>
            <span class="btn btn-primary" ng-click="struktura.showAddForm($event)">Добавить</span>
        </div>
        <br>
        <div class="thead div-row row nomargin">
            <div class="col-md-1 div-td center">№</div>
            <div class="col-md-6 div-td">Название</div>
            <div class="col-md-2 div-td center">Баллы</div>
            <div class="col-md-3 div-td">&nbsp;</div>
        </div>
        <div class="tfooter div-row row nomargin">
            <div class="col-md-1 div-td center">&nbsp;</div>
            <div class="col-md-6 div-td right">Итого</div>
            <div class="col-md-2 div-td center">{{struktura.getSummaBallov()}}</div>
            <div class="col-md-3 div-td">&nbsp;</div>
        </div>
        <div class="div-row row nomargin" ng-class="item.podstrukturaRel.length > 0 ? 'bold' : ''" ng-repeat-start="item in struktura.list | orderBy: 'nomer'">
            <div class="col-md-1 div-td">{{item.nomer}}</div>
            <div class="col-md-6 div-td struktura-nazvanie">{{item.nazvanie}}</div>
            <div class="col-md-2 div-td center">{{item.bally}}</div>
            <div class="col-md-3 div-td">
                <button title="Редактировать" data-toggle="tooltip" ng-attr-id="{{'editbtn'+item.id}}"  type="button" ng-click="struktura.showEditForm('editbtn'+item.id,item);" class="btn btn-default tool-btn" aria-label="Left Align">
                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                </button>

                <button title="Удалить" type="button" class="btn btn-default tool-btn" ng-click="struktura.deleteItem(item);" aria-label="Left Align">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </button>

                <button ng-attr-id="{{'addbtn'+item.id}}" title="Добавить подпункт" type="button" class="btn btn-default tool-btn" ng-click="struktura.showAddForm('addbtn'+item.id, item);" aria-label="Left Align">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </button>
            </div>
        </div>
        <div class="struktura-children div-row row nomargin normal" ng-repeat="poditem in item.podstrukturaRel">
            <div class="col-md-1 div-td">{{item.nomer}}.{{poditem.nomer}}</div>
            <div class="col-md-6 div-td struktura-nazvanie">{{poditem.nazvanie}}</div>
            <div class="col-md-2 div-td center">{{poditem.bally}}</div>
            <div class="col-md-3 div-td">
                <button title="Редактировать" ng-attr-id="{{'editbtn'+poditem.id}}" data-toggle="tooltip"  type="button" ng-click="struktura.showEditForm('editbtn'+poditem.id,poditem);" class="btn btn-default tool-btn" aria-label="Left Align">
                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                </button>

                <button title="Удалить" type="button" class="btn btn-default tool-btn" ng-click="struktura.deleteItem(poditem);" aria-label="Left Align">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </button>

            </div>
        </div>
        <span ng-repeat-end></span>
        <div class="tfooter div-row row nomargin">
            <div class="col-md-1 div-td center">&nbsp;</div>
            <div class="col-md-6 div-td right">Итого</div>
            <div class="col-md-2 div-td center">{{struktura.getSummaBallov()}}</div>
            <div class="col-md-3 div-td">&nbsp;</div>
        </div>
        <div class="add-form hidden" id="addForm">
            <form ng-submit="struktura.addItem()" class="form-inline">
                <div>
                    <input type="text" ng-model="struktura.newItem.nazvanie" style="width:400px"
                           placeholder="Название нового критерия" id="struktura-nazvanie" class="form-control">
                    <input type="number" style="width: 4.5em" ng-model="struktura.newItem.bally"
                           placeholder="" class="form-control" id="struktura-nomer" min="1">
                </div>
                <br>
                <div>
                    <input class="btn btn-primary" type="submit" value="Добавить">
                    <span class="slink" ng-click="struktura.closeAddForm()">Отмена</span>
                </div>
            </form>
        </div>

        <div class="add-form hidden" id="editForm">
            <form ng-submit="struktura.editStruktura()" class="form-inline">
                <div>
                    <input type="text" ng-model="struktura.editItem.nazvanie" style="width:400px"
                           placeholder="Название нового критерия" id="struktura-edit-nazvanie" class="form-control">
                    <input type="number" style="width: 4.5em" ng-model="struktura.editItem.bally"
                           ng-hide="struktura.editItem.podstrukturaRel.length > 0"
                           placeholder="" class="form-control" id="struktura-edit-nomer" min="1">
                </div>
                <br>
                <div>
                    <input class="btn btn-primary" type="submit" value="Сохранить">
                    <span class="slink" ng-click="struktura.closeEditForm()">Отмена</span>
                </div>
            </form>
        </div>
    </div>

    <div ng-controller="IspytaniyaController as isp" ng-show="isp.selectedList != -1" class="col-md-6">
        <form class="" ng-submit="isp.addIspytanie()">
            <div class="col-md-9 form-group no-padding">
                <label for="">Форма испытания</label>
                <?=
                    \app\helpers\Html::dropDownList('ispytaniya',null,[null=>'Выберите испытание']+$ispytaniyaList,[
                        'class' => 'form-control',
                        'placeholder' => 'Выберите испытание',
                        'ng-model' => 'isp.ispytanie'
                    ])
                ?>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button class="btn btn-primary">Добавить</button>
            </div>
        </form>
        <table class="att-tb" style="width: 100%;">
            <tr class="thead">
                <td style="width: 90%;">Название</td>
                <td>&nbsp;</td>
            </tr>
            <tr ng-repeat="item in isp.list">
                <td>{{item.nazvanie}}</td>
                <td>
                    <button title="Удалить" ng-hide="list.is_edit" type="button" class="btn btn-default tool-btn" ng-click="isp.deleteIspytanie(item);" aria-label="Left Align">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                    </button>
                </td>
            </tr>
        </table>
    </div>

</div>
