$(function(){

    var otsenochnyeListy = angular.module('otsenochnyeListy',[]);

    otsenochnyeListy.directive('stringToNumber', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                ngModel.$parsers.push(function(value) {
                    if (value == null) return null;
                    else return '' + value;
                });
                ngModel.$formatters.push(function(value) {
                    if (value == null) return null;
                    else return parseFloat(value, 10);
                });
            }
        };
    });

    otsenochnyeListy.controller('MainController',function($scope,$rootScope){
        var main = this;

        main.newNazvanie = '';
        main.minBallPervayKategoriya = null;
        main.minBallVisshayaKategoriya = null;

        main.otsenochnyeListy = [];

        main.otsenochnyeListyEdit = [];

        main.otsenochyjListExtend = {
            is_edit: false,
            is_selected: false,
            nazvanie_copy: '',
            min_ball_pervaya_kategoriya_copy: null,
            min_ball_visshaya_kategoriya_copy: null
        };

        briop_ajax({
            url: '/otsenochnyj-list/get-otsenochnye-listy',
            done: function(response){
                main.otsenochnyeListy = response.data;
                angular.forEach(main.otsenochnyeListy,function(item){
                    item = $.extend(item,main.otsenochyjListExtend);
                    item.nazvanie_copy = item.nazvanie;
                    item.min_ball_pervaya_kategoriya_copy = item.min_ball_pervaya_kategoriya;
                    item.min_ball_visshaya_kategoriya_copy = item.min_ball_visshaya_kategoriya;
                });
                $scope.$apply();
            }
        });

        main.addOtsenochnyjList = function(){
            if (main.minBallPervayKategoriya != null && main.minBallPervayKategoriya <= 0){
                bsalert('Количество баллов должно быть целым положительным числом','danger');
                return false;
            }
            if (main.minBallVisshayaKategoriya != null && main.minBallVisshayaKategoriya <= 0){
                bsalert('Количество баллов должно быть целым положительным числом','danger');
                return false;
            }
            if (main.newNazvanie == ''){
                bsalert('Введите название','danger');
                return false;
            }
            briop_ajax({
                url: '/otsenochnyj-list/add-otsenochnyj-list',
                data: {
                    nazvanie: main.newNazvanie,
                    minBallPervayKategoriya: main.minBallPervayKategoriya,
                    minBallVisshayaKategoriya: main.minBallVisshayaKategoriya
                },
                done: function(response){
                    if (response.type != 'error'){
                        var addedItem = $.extend(response.data,main.otsenochyjListExtend);
                        addedItem.nazvanie_copy = addedItem.nazvanie;
                        addedItem.min_ball_pervaya_kategoriya_copy = addedItem.min_ball_pervaya_kategoriya;
                        addedItem.min_ball_visshaya_kategoriya_copy = addedItem.min_ball_visshaya_kategoriya;
                        main.otsenochnyeListy.push(addedItem);
                        main.newNazvanie = '';
                        main.minBallPervayKategoriya = null;
                        main.minBallVisshayaKategoriya = null;
                        bsalert('Операция выполнена успешно', 'success');
                    }
                    else{
                        bsalert('Ошибка! Данные не сохранены!','danger');
                    }
                    $scope.$apply();
                }
            })
        };

        main.editOtsenochnyjList = function(item){
            var index = main.otsenochnyeListy.indexOf(item);
            main.otsenochnyeListy[index].is_edit = !main.otsenochnyeListy[index].is_edit;
            $('#list-nazvanie'+main.otsenochnyeListy[index].id).focus();
        };

        main.deleteOtsenochnyjList = function(item){
            if (confirm('Вы действительно хотите удалить лист "' + item.nazvanie + '"')) {
                briop_ajax({
                    url: '/otsenochnyj-list/delete-otsenochnyj-list',
                    data: {
                        id: item.id
                    },
                    done: function (response) {
                        if (response.type == 'success') {
                            var index = main.otsenochnyeListy.indexOf(item);
                            main.otsenochnyeListy.splice(index, 1);
                            bsalert('Сохранение успешно выполнено', 'success');
                        }
                        else bsalert('Запись не удалена, произошла ошибка во время выполнения запроса к базе данных', 'danger');
                        $scope.$apply();
                    }
                });
            }
        };

        main.cancelChanges = function(item){
            var index = main.otsenochnyeListy.indexOf(item);
            main.otsenochnyeListy[index].is_edit = !main.otsenochnyeListy[index].is_edit;
            main.otsenochnyeListy[index].nazvanie_copy = main.otsenochnyeListy[index].nazvanie;
            main.otsenochnyeListy[index].min_ball_pervaya_kategoriya_copy = main.otsenochnyeListy[index].min_ball_pervaya_kategoriya;
            main.otsenochnyeListy[index].min_ball_visshaya_kategoriya_copy = main.otsenochnyeListy[index].min_ball_visshaya_kategoriya;
        };

        main.commitChanges = function(item){
            var index = main.otsenochnyeListy.indexOf(item);
            if (item.nazvanie_copy == ''){
                bsalert('Введите новое название', 'warning','top');
                $('#list-nazvanie'+item.id).focus();
                return false;
            }
            if (item.min_ball_pervaya_kategoriya_copy != null && item.min_ball_pervaya_kategoriya_copy <= 0){
                bsalert('Количество баллов должно быть целым положительным числом','danger');
                return false;
            }
            if (item.min_ball_visshaya_kategoriya_copy != null && item.min_ball_visshaya_kategoriya_copy <= 0){
                bsalert('Количество баллов должно быть целым положительным числом','danger');
                return false;
            }
            if (main.otsenochnyeListy[index].nazvanie == main.otsenochnyeListy[index].nazvanie_copy
                && main.otsenochnyeListy[index].min_ball_pervaya_kategoriya_copy == main.otsenochnyeListy[index].min_ball_pervaya_kategoriya
                && main.otsenochnyeListy[index].min_ball_visshaya_kategoriya_copy == main.otsenochnyeListy[index].min_ball_visshaya_kategoriya
                ) {
                main.otsenochnyeListy[index].is_edit = false;
                return false;
            }
            briop_ajax({
                url: '/otsenochnyj-list/commit-edit-list',
                data:{
                    id: item.id,
                    new_nazvanie: item.nazvanie_copy,
                    new_min_ball_pervaya_kategoriya: item.min_ball_pervaya_kategoriya_copy,
                    new_min_ball_visshay_kategoriya: item.min_ball_visshaya_kategoriya_copy,
                },
                done: function(response){
                    if (response.type == 'success') {
                        main.otsenochnyeListy[index] = $.extend(response.data,main.otsenochyjListExtend);
                        main.otsenochnyeListy[index].nazvanie_copy = main.otsenochnyeListy[index].nazvanie;
                        main.otsenochnyeListy[index].min_ball_pervaya_kategoriya_copy = main.otsenochnyeListy[index].min_ball_pervaya_kategoriya;
                        main.otsenochnyeListy[index].min_ball_visshaya_kategoriya_copy = main.otsenochnyeListy[index].min_ball_visshaya_kategoriya;
                        bsalert('Сохранение успешно выполнено', 'success');
                    }
                    else{
                        bsalert(data.msg,'danger')
                    }
                },
                finally: function(){
                    $scope.$apply();
                }
            });
        };

        main.deSelect = function(){
            angular.forEach(main.otsenochnyeListy,function(v,k){
                v.is_selected = false;
            });
        }

        main.editStruktura = function(item){
            main.deSelect();
            item.is_selected = true;
            $rootScope.$broadcast('edit_struktura', item);
        }

        main.editIspytaniya =function(item){
            main.deSelect();
            item.is_selected = true;
            $rootScope.$broadcast('edit_ispytaniya', item);
        }
    });

    otsenochnyeListy.controller('StrukturaController',function($scope,$rootScope,$compile){
        var struktura = this;

        struktura.selectedList = -1;

        struktura.newItem = {
            nazvanie: '',
            bally: 1,
            roditel: null,
            nomer: 0
        };

        struktura.editItem = {};

        struktura.list = [];

        $scope.$on('edit_struktura', function(event,args){
            struktura.selectedList = args;
            console.log(struktura.selectedList);
            briop_ajax({
                url: '/otsenochnyj-list/get-struktura',
                data:{
                    list: struktura.selectedList.id
                },
                done: function(response){
                    struktura.list = response.data;
                },
                finally: function(){
                    $scope.$apply();
                }
            })
        });

        $scope.$on('edit_ispytaniya', function(event, args){
            struktura.selectedList = -1;
            struktura.list = [];
        });

        struktura.showAddForm = function(elementId,roditel){
            roditel = roditel || null;
            var element = $('#'+elementId);
            if (roditel != null){
                struktura.newItem.roditel = roditel.id;
            }
            if (roditel != null){
                var parent = element.parent().parent();
                element = parent.find('.struktura-nazvanie');
            }
            var addForm = $('#addForm');
            var offset = element.position();
            addForm.css('top',offset.top);
            //element.before(editForm);
            addForm.removeClass('hidden');
            setTimeout(function(){ $('#addForm').addClass('add-form-shown'); },100)
        };

        struktura.closeAddForm = function(){
            struktura.newItem = {
                nazvanie: '',
                bally: 1,
                roditel: null,
                nomer: 0
            };
            var addForm = $('#addForm');
            addForm.removeClass('add-form-shown');
            setTimeout(function(){
                addForm.addClass('hidden');
                //$('#struktura').append(addForm);
            },200);
        };

        struktura.setMaxNomer = function(){
            var array = struktura.list;
            if (struktura.newItem.roditel != null){
                var roditel_index = struktura.findIndexItemById(struktura.newItem.roditel);
                array = struktura.list[roditel_index].podstrukturaRel;
            }
            if (array.length == 0) struktura.newItem.nomer = 1;
            else struktura.newItem.nomer = array[array.length - 1].nomer + 1;
        }

        struktura.findIndexItemById = function(id){
            var result = -1;
            $(struktura.list).each(function(index, item){
                if (item.id == id){
                    result = index;
                    return false;
                }
            });
            return result;
        };

        struktura.getSummaBallov = function(){
            var summa = 0;
            angular.forEach(struktura.list,function(item){
               summa += Number(item.bally);
            });
            return summa;
        }

        struktura.addItem = function(){
            if (struktura.newItem.nazvanie == ''){
                bsalert('Введите название','warning');
                $('#struktura-nazvanie').focus();
                return false;
            }
            if (struktura.newItem.bally == '' || struktura.newItem.bally < 0){
                bsalert('Введите количество баллов, целое положительное число','warning');
                $('#struktura-nomer').focus();
                return false;
            }
            struktura.setMaxNomer();
            briop_ajax({
                url: '/otsenochnyj-list/add-struktura',
                data:{
                    nazvanie: struktura.newItem.nazvanie,
                    bally: struktura.newItem.bally,
                    roditel: struktura.newItem.roditel,
                    nomer: struktura.newItem.nomer,
                    otsenochnyj_list: struktura.selectedList.id
                },
                done: function(response){
                    if (response.type == 'success' ) {
                        if (struktura.newItem.roditel == null) {
                            struktura.list.push(response.data);
                        }
                        else{
                            var roditel_index = struktura.findIndexItemById(struktura.newItem.roditel);
                            struktura.list[roditel_index] =response.data;// .podstrukturaRel.push(response.data);
                        }
                        bsalert(response.msg,'success');
                    }
                    else{
                        bsalert(response.msg,'danger');
                    }
                    struktura.closeAddForm();
                    $scope.$apply();
                }
            })
        };

        struktura.deleteItem = function(item){
            if (confirm('Вы действительно хотите удалить "'+ item.nazvanie +'"')){
                briop_ajax({
                    url: '/otsenochnyj-list/delete-struktura',
                    data:{
                        id: item.id
                    },
                    done: function(response){
                        if (response.type == 'success'){
                            var index;
                            if (item.roditel == null){
                                index = struktura.findIndexItemById(item.id);
                                angular.forEach(struktura.list,function(value, key){
                                    if (key > index){
                                        struktura.list[key].nomer -= 1;
                                    }
                                });
                                struktura.list.splice(index,1);
                            }
                            else{
                                index = struktura.findIndexItemById(response.data.id);
                                struktura.list[index] = response.data;
                            }
                            bsalert(response.msg,'success');
                        }
                        else{
                            bsalert(response.msg,'danger');
                        }
                        $scope.$apply();
                    }
                });
            }
        }

        struktura.showEditForm = function(elementId,item){
            var element = $('#'+elementId);
            struktura.editItem = item;
            var parent = element.parent().parent();
            element = parent.find('.struktura-nazvanie');
            var editForm = $('#editForm');
            var offset = element.position();
            editForm.css('top',offset.top);
            //element.before(editForm);
            editForm.removeClass('hidden');
            setTimeout(function(){ $('#editForm').addClass('add-form-shown'); },100)
        }

        struktura.closeEditForm = function(){
            struktura.editItem = {
                nazvanie: '',
                bally: 1,
                roditel: null,
                nomer: 0
            };
            var editForm = $('#editForm');
            editForm.removeClass('add-form-shown');
            setTimeout(function(){
                editForm.addClass('hidden');
                //$('#struktura').append(addForm);
            },200);
        }

        struktura.editStruktura = function(){
            if (struktura.editItem.nazvanie == ''){
                bsalert('Введите название','warning');
                $('#struktura-edit-nazvanie').focus();
                return false;
            }
            if (struktura.editItem.bally == '' || struktura.newItem.bally < 0){
                bsalert('Введите количество баллов, целое положительное число','warning');
                $('#struktura-edit-nomer').focus();
                return false;
            }
            briop_ajax({
                url: '/otsenochnyj-list/edit-struktura',
                data:{
                    id: struktura.editItem.id,
                    nazvanie: struktura.editItem.nazvanie,
                    bally: struktura.editItem.bally,
                },
                done: function(response){
                    if (response.type == 'success' ) {
                        var roditel_index = struktura.findIndexItemById(response.data.id);
                        struktura.list[roditel_index] =response.data;// .podstrukturaRel.push(response.data);
                        bsalert(response.msg,'success');
                    }
                    else{
                        bsalert(response.msg,'danger');
                    }
                    struktura.closeEditForm();
                    $scope.$apply();
                }
            });
        }

    });

    otsenochnyeListy.controller('IspytaniyaController',function($scope,$rootScope){
        var isp = this;

        isp.selectedList = -1;
        isp.list = [];

        isp.ispytanie = null;

        $scope.$on('edit_ispytaniya',function(event, args){
            isp.selectedList = args;
            briop_ajax({
                url: '/otsenochnyj-list/get-ispytaniya',
                data: {
                    otsenochnyjList: isp.selectedList.id
                },
                done: function(response){
                    isp.list = response.data;
                    $scope.$apply();
                }
            });

        });

        $scope.$on('edit_struktura', function(event,args){
            isp.selectedList = -1;
            isp.list = [];
        });

        isp.addIspytanie = function(){
            if (isp.ispytanie == null){
                bsalert('Выберите испытание','warning');
                return false;
            }
            briop_ajax({
                url: '/otsenochnyj-list/add-ispytanie',
                data:{
                    ispytanie: isp.ispytanie,
                    otsenochnyjList: isp.selectedList.id
                },
                done: function(response){
                    if (response.type == 'success'){
                        isp.list.push(response.data);
                        bsalert(response.msg,'success')
                    }
                    else{
                        bsalert(response.msg,'danger');
                    }
                    $scope.$apply();
                }
            });
        }

        isp.deleteIspytanie = function(item){
            if (confirm('Вы действительно хотите удалить ' + item.nazvanie + '?')) {
                briop_ajax({
                    url: '/otsenochnyj-list/delete-ispytanie',
                    data: {
                        id: item.id
                    },
                    done: function (response) {
                        if (response.type == 'success') {
                            var index = isp.list.indexOf(item);
                            isp.list.splice(index, 1);
                            bsalert(response.msg, 'success');
                        }
                        else {
                            bsalert(response.msg, 'danger');
                        }
                        $scope.$apply();
                    }
                })
            }
        };

    });

    otsenochnyeListy.directive('strukturaRow',function(){
        return {
            restrict: 'E',
            scope: {
              dataItem: '='
            },
            template:
            '<div class="row nomargin div-row">'+
            '<div class="col-md-1 div-td center">{{dataItem.nomer}}</div>'+
            '<div class="col-md-6 div-td struktura-nazvanie">{{dataItem.nazvanie}}</div>'+
            '<div class="col-md-2 div-td center">{{dataItem.bally}}</div>'+
            '<div class="col-md-3 div-td">'+
                '<button title="Редактировать" data-toggle="tooltip"  type="button" ng-click="struktura.editItem(dataItem);" class="btn btn-default tool-btn" aria-label="Left Align">'+
                '<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>'+
                '</button>'+

                '<button title="Удалить" type="button" class="btn btn-default tool-btn" ng-click="struktura.deleteItem(dataItem);" aria-label="Left Align">'+
                '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>'+
                '</button>'+

                '<button title="Редактировать стуктуру листа" type="button" class="btn btn-default tool-btn" ng-click="struktura.showAddForm($event,dataItem);" aria-label="Left Align">'+
                '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>'+
                '</button>'+
            '</div>'+
            '</div>'
        };
    });

    var otsenochnyeListyApp = document.getElementById('otsenochnye-listy');

    angular.element(document).ready(function() {
        angular.bootstrap(otsenochnyeListyApp, ['otsenochnyeListy']);
    });
})
