var app = angular.module('otsenki',['ngSanitize']);

app.controller('SpisokController',function($scope, $rootScope){
    var s = this;
    s.period = $('#periods option:selected').val();

    s.spisok = [];

    s.is_show = true;

    s.allUnfinished = false;

    s.loadZayavleniya = function(){
        var period_id = s.allUnfinished ? null : s.period;
        console.log(period_id);
        briop_ajax({
            url: '/sotrudnik-att-komissii/get-zayvleniya',
            data: {
                period_id: period_id,
                all_unfinished: (s.allUnfinished ? 1 : 0)
            },
            done: function(response){
                s.spisok = response.data;
                $scope.$apply();
            },
        });
    };

    s.putMarks = function(zayavlenieId){
        $rootScope.$broadcast('otsenki', zayavlenieId);
    };

    s.toggleUnfinished = function(){

    };

    s.currentZayavlenieContent = '';
    s.hide_zayvlenie = true;

    s.getZayavlenie = function(zayavlenieId){
        var id = zayavlenieId;
        briop_ajax({
            url: '/attestaciya/zayavlenie',
            data: {
                isAjax: 1,
                id: id
            },
            done: function (data){
                s.currentZayavlenieContent = data;
                s.hide_zayvlenie = false;
                $scope.$apply();
            },
        });
    }

    s.backToList = function(){
        s.hide_zayvlenie = true;
        s.currentZayavlenieContent = '';
    }

    $scope.$on('toggleZayavleniya',function(event,args){
        s.is_show = args;
    });
});

app.controller('OtsenkiController', function($scope, $rootScope){
    var o = this;

    o.zayavlenieId = -1;

    o.lists = [];
    o.helpList = []; //для того, чтобы записать туда, есть у определенного листа дети

    o.is_show = false;
    o.maxSumms = {};

    $scope.$on('otsenki', function(event,args){
        o.zayavlenieId = args;
        briop_ajax({
            url: '/sotrudnik-att-komissii/otsenki',
            data:{
                zayavlenie_id: o.zayavlenieId,
                ajax: 1
            },
            done: function(response){
                if (response.type != 'error'){
                    //bsalert(response.msg,'success');
                    o.lists = response.data;
                    $rootScope.$broadcast('toggleZayavleniya',false);
                    o.is_show = true;
                }
                else{
                    bsalert(response.msg,'danger');
                }
            },
            finally: function(){
                $scope.$apply();
            }
        });
    });

    o.areThereChildren = function(struktura,item){
        var result = false;
        if (o.helpList.hasOwnProperty(item.id)){
            result =  o.helpList[item.id];
        }
        else {
            for (var i = 0, length = struktura.length; i < length; i++) {
                if (struktura[i].roditel == item.struktura_otsenochnogo_lista) {
                    result = true;
                    break;
                }
            }
            var id = item.id;
            o.helpList.id = result;
        }
        return result;
    }

    o.changeMark = function(struktura,item){
        if (item.max_bally < item.bally){
            item.bally = item.max_bally;
        }
        var result = 0;
        for (var i = 0, length = struktura.length; i < length; i++) {
            if (struktura[i].roditel == item.roditel) {
                result += struktura[i].bally;
            }
        }
        for (var i = 0, length = struktura.length; i < length; i++) {
            if (struktura[i].struktura_otsenochnogo_lista == item.roditel) {
                struktura[i].bally = result;
                break;
            }
        }
    }

    o.back = function(){
        o.is_show = false;
        o.zayavlenieId = -1;
        o.lists = [];
        o.helpList = [];
        $rootScope.$broadcast('toggleZayavleniya',true);
    }

    o.saveOtsenki = function(list){
        briop_ajax({
            url: '/sotrudnik-att-komissii/save-otsenki',
            data: {
                list: list
            },
            done: function(response){
                if (response.type != 'error'){
                    bsalert(response.msg,'success');
                    list.status = response.data;
                }
                else{
                    bsalert(response.msg,'danger');
                }
            },
            finally: function(){
                $scope.$apply();
            }
        });
    }

    o.calculateMaxSumm = function(list){
        if (o.maxSumms.hasOwnProperty(list.id)){
            return o.maxSumms.hasOwnProperty(list.id);
        }
        result = 0;
        for (var i = 0, length = list.struktura.length; i < length; i++) {
            if (list.struktura[i].uroven == 1) {
                result += list.struktura[i].max_bally;
            }
        }
        var id = list.id;
        o.maxSumms.id = result;
        return result;
    }

    o.calculateSumm = function(list){
        result = 0;
        for (var i = 0, length = list.struktura.length; i < length; i++) {
            if (list.struktura[i].uroven == 1) {
                result += list.struktura[i].bally;
            }
        }
        return result;
    }

});
