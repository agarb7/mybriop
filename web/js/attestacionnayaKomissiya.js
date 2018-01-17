$(function(){

    var app = angular.module('komissii',[]);

    app.controller('KomissiiListController',function($scope,$rootScope,$http){
        var komissii = this;

        komissii.list = [];
        briop_ajax({
            url: '/attestacionnaya-komissiya/get-komissii',
            data:{

            },
            done: function(data){
                komissii.list = data;
                angular.forEach(komissii.list,function(item){
                    item.is_edit = false;
                    item.is_selected = false;
                    item.nazvanie_copy = item.nazvanie;
                });
                $('#komissii').removeClass('hidden');
                $scope.$apply();
            }
        });

        komissii.addKomissiyu = function(){
            briop_ajax({
                url: '/attestacionnaya-komissiya/add-komissiyu',
                data:{
                    nazvanie: komissii.newNazvanie
                },
                done: function(data){
                    data.is_edit = false;
                    data.is_selected = false;
                    komissii.list.push(data);
                    bsalert('Сохранение успешно выполнено','success');
                },
                finally: function(){
                    komissii.newNazvanie = '';
                    $scope.$apply();
                }
            })

        }

        komissii.deleteKomissiyu = function (item) {
            if (confirm('Выдействительно хотите удалить комиссию?')){
                briop_ajax({
                    url: '/attestacionnaya-komissiya/delete-komissiyu',
                    data:{
                        id: item.id
                    },
                    done: function(data){
                        if (data == 1) {
                            var index = komissii.list.indexOf(item);
                            komissii.list.splice(index,1);
                            bsalert('Сохранение успешно выполнено', 'success');
                        }
                        else bsalert('Запись не удалена, произошла ошибка во время выполнения запроса к базе данных','danger');
                    },
                    finally: function(){
                        komissii.newNazvanie = '';
                        $scope.$apply();
                    }
                });
            }
        }

        komissii.editKomissiyu = function(item){
            var index = komissii.list.indexOf(item);
            komissii.list[index].is_edit = !komissii.list[index].is_edit;
            $('#input_nazvanie'+komissii.list[index].id).focus();
        }

        komissii.banChanges = function(item){
            var index = komissii.list.indexOf(item);
            komissii.list[index].is_edit = !komissii.list[index].is_edit;
            komissii.list[index].nazvanie_copy = komissii.list[index].nazvanie;
        }

        komissii.commitChanges = function(item){
            var index = komissii.list.indexOf(item);
            if (item.nazvanie_copy == ''){
                bsalert('Введите новое название', 'warning','top');
                $('#input_nazvanie'+item.id).focus();
                return false;
            }
            if (komissii.list[index].nazvanie == komissii.list[index].nazvanie_copy) {
                komissii.list[index].is_edit = false;
                return false;
            }
            briop_ajax({
                url: '/attestacionnaya-komissiya/commit-edit-komissii',
                data:{
                    id: item.id,
                    new_nazvanie: item.nazvanie_copy
                },
                done: function(data){
                    if (data.type == 'success') {
                        komissii.list[index].nazvanie = komissii.list[index].nazvanie_copy;
                        komissii.list[index].is_edit = false;
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
        }

        komissii.deSelect = function(){
            angular.forEach(komissii.list,function(v,k){
                v.is_selected = false;
            });
        }

        komissii.editDolzhnosti = function(item){
            komissii.deSelect();
            item.is_selected = true;
            $rootScope.$broadcast('edit_dolzhnosti', item);
        }

        komissii.editRabotnikov = function(item){
            komissii.deSelect();
            item.is_selected = true;
            $rootScope.$broadcast('edit_rabotnikov', item);
        }

    });

    app.controller('DolzhnostiListController',function($scope,$rootScope){
        var dolzhnosti = this;

        dolzhnosti.selected_komissiya = {};
        dolzhnosti.list = {};
        dolzhnosti.dolzhnost = -1;

        $scope.$on('edit_dolzhnosti', function(event,args){
            dolzhnosti.selected_komissiya = args;
            briop_ajax({
                url: '/attestacionnaya-komissiya/get-dolzhnosti',
                data:{
                    komissiya: dolzhnosti.selected_komissiya.id
                },
                done: function(data){
                    dolzhnosti.list = data;
                },
                finally: function(){
                    $scope.$apply();
                }
            })
        });

        $scope.$on('edit_rabotnikov',function(event,args){
           dolzhnosti.selected_komissiya = {};
        });

        dolzhnosti.addDolzhnost = function(){
            //console.log(dolzhnosti.dolzhnost);
            if (dolzhnosti.dolzhnost != -1)
            briop_ajax({
                url: '/attestacionnaya-komissiya/add-dolzhnost-to-komissiya',
                data:{
                    dolzhnost_id: dolzhnosti.dolzhnost,
                    komissiya_id: dolzhnosti.selected_komissiya.id
                },
                done: function(data){
                    if (data.type == 'success') {
                        dolzhnosti.list.push(data.data);
                        $('#dolzhnosti_select').select2('val','');
                        dolzhnosti.dolzhnost = -1;
                        bsalert('Сохранение успешно выполнено', 'success');
                    }
                    else
                        bsalert(data.msg,'danger','top');
                },
                finally: function(){
                    $scope.$apply();
                }
            })
        }

        dolzhnosti.deleteDolzhnost = function(item){
            if (confirm('Выдействительно хотите открепить должность?')){
                briop_ajax({
                    url: '/attestacionnaya-komissiya/delete-dolzhnost-from-komissiya',
                    data:{
                        dolzhnost_id: item.id,
                        komissiya_id: dolzhnosti.selected_komissiya.id
                    },
                    done: function(data){
                        if (data == 1) {
                            var index = dolzhnosti.list.indexOf(item);
                            dolzhnosti.list.splice(index,1);
                            bsalert('Сохранение успешно выполнено', 'success');
                        }
                        else bsalert('Запись не удалена, произошла ошибка во время выполнения запроса к базе данных','danger');
                    },
                    finally: function(){
                        $scope.$apply();
                    }
                });
            }
        }
    });


    app.controller('RabotnikiListController',function($scope,$rootScope){
        var rabotniki = this;
        rabotniki.list = [];
        rabotniki.selected_komissiya = {};
        rabotniki.rabotnik = -1;
        rabotniki.is_show_time = false;
        rabotniki.periods = [];
        rabotniki.id = '';
        rabotniki.nachalo = '';
        rabotniki.konec = '';

        $scope.$on('edit_dolzhnosti', function(event,args){
           rabotniki.selected_komissiya = {};
        });

        $scope.$on('edit_rabotnikov',function(event,args){
            rabotniki.selected_komissiya = args;
            briop_ajax({
                url: '/attestacionnaya-komissiya/get-rabotnikov-komissii',
                data: {
                    komissiya_id: rabotniki.selected_komissiya.id
                },
                done: function(data){
                    rabotniki.list = data
                },
                finally:function(){
                    $scope.$apply();
                }
            })
        });

        rabotniki.addRabotnika = function(){
            if (rabotniki.rabotnik != -1){
                briop_ajax({
                    url: '/attestacionnaya-komissiya/add-rabotnika-komissii',
                    data: {
                        komissiya_id: rabotniki.selected_komissiya.id,
                        rabotnik_id: rabotniki.rabotnik
                    },
                    done: function(data){
                        if (data.type == 'success'){
                            console.log(data.rabotnik.fizLicoRel);
                            rabotniki.list.push(data.rabotnik);
                            $('#rabotnik').select2('val','');
                            rabotniki.rabotnik = -1;
                            bsalert('Сохранение успешно выполнено', 'success');
                        }
                        else if(data.type == 'warning'){
                            bsalert(data.msg,'warning');
                        }
                        else{
                            bsalert(data.msg,'danger');
                        }
                    },
                    finally: function(){
                        $scope.$apply();
                    }
                })
            }
        }

        rabotniki.deleteRabotnika = function(item){
            if (confirm('Вы дейтвительно хотите убрать '
                        +item.fizLicoRel.familiya+' '+item.fizLicoRel.imya+' '
                        +item.fizLicoRel.otchestvo+' из комиссии?')){
                briop_ajax({
                    url: '/attestacionnaya-komissiya/delete-rabotnika-komissii',
                    data:{
                        id: item.id
                    },
                    done: function(data){
                        if (data.type == 'success'){
                            var index = rabotniki.list.indexOf(item);
                            rabotniki.list.splice(index,1);
                            bsalert('Операция успешно выполнена','success');
                        }
                        else if (data.type == 'error'){
                            bsalert(data.msg || 'Ошибка выполнения запроса, запись не удалена','danger')
                        }
                    },
                    finally: function(){
                        $scope.$apply();
                    }
                })
            }
        }

        rabotniki.timeRabotnika = function(item){
            var data_nachalo = (item.nachaloRel.nachalo).split("-");
            var data_konec = (item.konecRel.konec).split("-");
            console.log(item.nachaloRel.nachalo,item.nachaloRel.nachalo);
            rabotniki.is_show_time = true;
            rabotniki.fio = item.fizLicoRel.familiya+' '+item.fizLicoRel.imya+' '+item.fizLicoRel.otchestvo;
            rabotniki.id = item.id;
            rabotniki.nachalo = data_nachalo[2]+'.'+data_nachalo[1]+'.'+data_nachalo[0];
            rabotniki.konec = data_konec[2]+'.'+data_konec[1]+'.'+data_konec[0];
            console.log(rabotniki.nachalo,rabotniki.konec)
            briop_ajax({
                url: '/attestacionnaya-komissiya/get-period',
                data:{
                    komissiya: rabotniki.selected_komissiya.id
                },
                done: function(data){
                    rabotniki.periods = data;
                },
                finally: function(){
                    $scope.$apply();
                }
            });
        }

        rabotniki.closeTimeRabotnika = function(){
            rabotniki.is_show_time = false;
        }

        rabotniki.changeTimeRabotnika = function (period) {
            console.log(rabotniki.period, rabotniki.id);
            briop_ajax({
                url: '/attestacionnaya-komissiya/change-time-rabotnika',
                data:{
                    t: rabotniki.period,
                    id: rabotniki.id,
                },
                done: function(data){
                    if (data.type == 'success'){
                        rabotniki.is_show_time = false;
                        bsalert('Операция успешно выполнена','success');
                    }
                    else if (data.type == 'error'){
                        rabotniki.is_show_time = false;
                        bsalert(data.msg || 'Ошибка выполнения запроса, запись не изменена','danger')
                    }
                },
                finally: function(){
                    $scope.$apply();
                }
            })
        }

        rabotniki.setPredsedatel = function(item){
            briop_ajax({
                url: '/attestacionnaya-komissiya/change-predsedatel-komissii',
                data: {
                    id: item.id
                },
                done: function(answer){
                    if (answer.type == 'success'){
                        if (answer.data.predsedatel) {
                            angular.forEach(rabotniki.list, function (value, key) {
                                    if (value.id != item.id) rabotniki.list[key].predsedatel = false;
                            });
                        }
                        bsalert(answer.msg);
                    }
                    else{
                        item.predsedatel = !item.predsedatel;
                        bsalert(answer.msg,data.type);
                    }
                },
                fail: function(){
                    item.predsedatel = !item.predsedatel;
                },
                finally: function(){
                    $scope.$apply();
                }
            })
        }
    });

})



