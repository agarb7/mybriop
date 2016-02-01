$(function(){

    var app = angular.module('rabochaya_programma_copying',[]);

    app.controller('CopyingController',function($scope,$rootScope){
        var copying = this;
        copying.year = $('#plan_prospekt_years option:selected').val();

        $scope.$watch('copying.year',function(newValue,oldValue){
            if (copying.to != -1) copying.to = -1;
            copying.loadKursy();
        })

        copying.from = -1;
        copying.to = -1;

        copying.kursy = {};

        copying.isShow = false;

        copying.loadKursy = function(){
            if (copying.kursy.hasOwnProperty(copying.year)) return true;

            briop_ajax({
                url: '/kursy-rukovoditelya/get-kursy-by-year/',
                data: {
                    year: copying.year
                },
                done: function(response){
                    copying.kursy[copying.year] = response.data;
                    copying.to = -1;
                    $scope.$apply();
                }
            });

        };

        copying.makeCopy = function() {
            console.log(copying.from, copying.to);
            if (copying.from == -1) return false;
            if (copying.to == -1) return false;
            var doCopy = function () {
                briop_ajax({
                    url: '/kursy-rukovoditelya/copy-program',
                    data: {
                        from: copying.from,
                        to: copying.to
                    },
                    done: function (response) {
                        if (response.type == 'error') {
                            bsalert(response.msg, 'danger');
                        }
                        else {
                            bsalert(response.msg);
                            copying.cancelCopying();
                        }
                        $scope.$apply();
                    }
                });
            };

            briop_ajax({
                url:'/kursy-rukovoditelya/check-program-existence',
                data:{
                    kurs_id: copying.to
                },
                done: function(response){
                    var isCopy = true;
                    if (response.data == true){
                        if (!confirm('Внимание! У курса, для которого вы хотите скопировать программу,' +
                                ' имееются уже заполненные данные. Все заполненные данные' +
                                ' будут утеряны. Продолжить копирование?')){
                            isCopy = false;
                        }
                    }
                    if (isCopy) doCopy();
                }
            });

        }

        copying.chooseKurs = function(kurs_id){
          copying.to = kurs_id;
        };

        copying.cancelCopying = function(){
            copying.to = -1;
            copying.from = -1;
            copying.isShow = false;
            $rootScope.$broadcast('cancelCopying');
        };

        $scope.$on('copy_kurs',function(event,args){
            copying.from = args;
            var offset = $('#kurs'+copying.from).position();
            var offset_height = $('#kurs'+copying.from).height();
            offset.top = offset.top + offset_height-5;
            $('#copying-form').css('top',offset.top);
            $('html, body').animate({
                scrollTop: $('#kurs'+copying.from).offset().top
            }, 500);
            copying.isShow = true;
        });
    });

    app.controller('MainController',function($scope,$rootScope){
        var main = this;

        main.currentKurs = -1;

        main.copyProgram = function(kurs_id){
            $rootScope.$broadcast('copy_kurs',kurs_id);
            main.currentKurs = kurs_id;
        }

        $scope.$on('cancelCopying',function(){
            main.currentKurs = -1;
        })

        main.deleteProgram = function(kurs_id){
            var kurs_nazvanie = $('#kurs_nazvanie'+kurs_id).text();
            if (confirm('Вы действительно хотите удалить программу курса «'+ kurs_nazvanie +'»?')){
                briop_ajax({
                    url: '/kursy-rukovoditelya/delete-program',
                    data: {
                        kurs_id: kurs_id
                    },
                    done: function(response){
                        if (response.type == 'error'){
                            bsalert(response.msg,'danger');
                        }
                        else{
                            bsalert(response.msg);
                        }
                    }
                })
            }
        }
    })

})

