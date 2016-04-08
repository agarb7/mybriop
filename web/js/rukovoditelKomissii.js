$(function() {

    var app = angular.module('rukovoditel',[]);

    app.controller('RukovoditelKomissiiController',function($scope,$rootScope,$http){
        var rk = this;
        rk.is_show_table = false;
        rk.rabotniki = [];

        $http.get('/rukovoditel-komissii/get-rabotniki-komissii')
             .then(function(response){
                console.log(response);
                rk.rabotniki = [];
                rk.rabotniki = response.data;
             });

        rk.zayavleniya = [];

        rk.loadZayavleniya = function(){
            var period = $('#periods option:selected').val();
            $http.get('/rukovoditel-komissii/get-zayavleniya',{
                params:{
                    period: period
                }
            })
            .then(function(response){
                rk.zayavleniya = response.data;
                rk.is_show_table = true;
                console.log(response.data);
            });
        }

        rk.checkAll = function(){
            rk.zayavleniya.forEach(function(z,i){
                if (z.raspredelenieCopy.length == 0) {
                    angular.forEach(rk.rabotniki,function(rabotnik,index){
                        if (rabotnik.checked) z.raspredelenieCopy.push(rabotnik.rabotnikId);
                    })
                }
            });
            console.log(rk.zayavleniya);
        }

        rk.checkOne = function(zayavlenie, rabotnikId){
            var index = zayavlenie.raspredelenieCopy.indexOf(rabotnikId);
            if (index == -1)
                zayavlenie.raspredelenieCopy.push(rabotnikId);
            else
                zayavlenie.raspredelenieCopy.splice(index,1);
        }

        rk.saveChanges = function(){
            $modifiedZayavleniya = [];
            rk.zayavleniya.forEach(function(e,i){
                if (!angular.equals(e.raspredelenie.sort(),e.raspredelenieCopy.sort())) $modifiedZayavleniya.push(e);
            });
            if ($modifiedZayavleniya.length > 0){
                briop_ajax({
                    url: '/rukovoditel-komissii/save-raspredelenie',
                    data: {'zayavleniya':$modifiedZayavleniya},
                    done: function(response){
                        rk.zayavleniya.forEach(function(e,i){
                            var clone = [];
                            e.raspredelenieCopy.forEach(function(er,ir){
                                clone[ir] = e.raspredelenieCopy[ir];
                            });
                            e.raspredelenie = clone;
                        });
                        if (response.type != 'error'){
                            bsalert(response.msg,'success');
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
            console.log($modifiedZayavleniya);
        }

        rk.avgBall = function(rabotnikId, otsenki){
            result = null;
            if (otsenki.hasOwnProperty(rabotnikId)){
                var avg = 0;
                for(var i = 0,length = otsenki[rabotnikId].length;i<length;i++){
                    if (otsenki[rabotnikId][i].bally)
                        avg += otsenki[rabotnikId][i].bally;
                }
                avg /= otsenki[rabotnikId].length;
                result = avg.toFixed((2));
            }
            return result;
        }

        rk.showBally = function(e){
            var element = $(e.target);
            element.next('.bally-bubble').toggleClass('hidden');
        }

        rk.hideBallyBuble = function(e){
            var element = $(e.target);
            element.closest('.bally-bubble').addClass('hidden');
        }

        rk.resetBally = function(list){
            if (confirm('Выдействительно хотите обнулить оценки по оценочному листу "' + list.nazvanie + '"')) {
                briop_ajax({
                    url: '/rukovoditel-komissii/reset-bally',
                    data: {
                        id: list.id
                    },
                    done: function (response) {
                        if (response.type != 'error'){
                            list.bally = undefined;
                            bsalert(response.msg, 'success');
                        }
                        else{
                            bsalert(response.msg, 'danger');
                        }
                    },
                    finally: function () {
                        $scope.$apply();
                    }
                });
            }
        }

    });

});
