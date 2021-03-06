$(function() {

    var app = angular.module('rukovoditel',['ngSanitize']);

    app.controller('RukovoditelKomissiiController',function($scope,$rootScope,$http){
        var rk = this;
        rk.is_show_komissii = false;
        rk.is_show_table = false;
        rk.komissii = [];
        rk.rabotniki = [];
        rk.rabotnikiFio = {};
        rk.allUnfinished = false;
        rk.komissiya = $('#komissiya option:first').val();
        rk.hide_zayvlenie = true;
        rk.currentZayavlenieContent = '';
        
        rk.loadKomissii = function () {
            $http.get('/rukovoditel-komissii/get-komissii', {
                params: {
                    period: rk.period
                }
            })
                .then(function(response){
                    rk.is_show_komissii = true;
                    rk.komissiya = null;
                    rk.rabotniki = [];
                    rk.zayavleniya =[];
                    rk.komissii = response.data;
                    console.log(response.data);
                });
        }


        rk.loadRabotniki = function () {
            $http.get('/rukovoditel-komissii/get-rabotniki-komissii', {
                params: {
                    komissiya: rk.komissiya,
                    period: rk.period,
                }
            })
                .then(function (response) {
                    console.log(response);
                    rk.rabotniki = [];
                    angular.forEach(response.data, function (item, index) {
                        rk.rabotniki.push(item);
                        rk.rabotnikiFio[index] = item.familiya + ' ' + item.imya + ' ' + item.otchestvo;
                    });
                    rk.zayavleniya = [];
                    console.log(rk.rabotnikiFio);
                });
        };

        rk.loadRabotniki();

        rk.zayavleniya = [];

        rk.objectLen = function(object){
            var size = 0, key;
            for (key in object) {
                if (object.hasOwnProperty(key)) size++;
            }
            return size;
        }

        rk.loadZayavleniya = function(){
            var period = $('#periods option:selected').val();
            var komissiya = $('#komissiya').is('select')
                ? $('#komissiya option:selected').val()
                : $('#komissiya').val();
            $http.get('/rukovoditel-komissii/get-zayavleniya',{
                params:{
                    period: period,
                    komissiya: komissiya,
                    allUnfinished: rk.allUnfinished
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
                        // rk.zayavleniya.forEach(function(e,i){
                        //     var clone = [];
                        //     e.raspredelenieCopy.forEach(function(er,ir){
                        //         clone[ir] = e.raspredelenieCopy[ir];
                        //     });
                        //     e.raspredelenie = clone;
                        // });
                        rk.loadZayavleniya();
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
                var count = 0;
                for(var i = 0,length = otsenki[rabotnikId].length;i<length;i++){
                    if (otsenki[rabotnikId][i].bally && otsenki[rabotnikId][i].bally!=0) {
                        avg += otsenki[rabotnikId][i].bally;
                        count++;
                    }
                }
                if (avg != 0) avg /= count;
                result = avg.toFixed((2));
            }
            return result;
        }

        rk.showBally = function(zayavlenieId){
            //var element = $(e.target);
            //element.next('.bally-bubble').toggleClass('hidden');
            $('#otsenki_'+zayavlenieId).toggleClass('hidden');
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

        rk.signOtsenki = function(item){
            var fio = rk.rabotnikiFio[item.rabotnikAttestacionnojKomissiiRel.fiz_lico];
                      //rk.rabotniki[item.rabotnikAttestacionnojKomissiiRel.fiz_lico].familiya + ' ' +
                      //rk.rabotniki[item.rabotnikAttestacionnojKomissiiRel.fiz_lico].imya + ' ' +
                      //rk.rabotniki[item.rabotnikAttestacionnojKomissiiRel.fiz_lico].otchestvo;
            if (confirm('Выдействительно хотите подписать оценки ' + fio + '?')) {
                briop_ajax({
                    url: '/rukovoditel-komissii/sign-otsenki',
                    data: {
                        id: item.id
                    },
                    done: function (response) {
                        if (response.type != 'error'){
                            item.status = response.data;
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

        rk.unsignOtsenki = function(item){
            var fio = rk.rabotnikiFio[item.rabotnikAttestacionnojKomissiiRel.fiz_lico];
            //rk.rabotniki[item.rabotnikAttestacionnojKomissiiRel.fiz_lico].familiya + ' ' +
            //rk.rabotniki[item.rabotnikAttestacionnojKomissiiRel.fiz_lico].imya + ' ' +
            //rk.rabotniki[item.rabotnikAttestacionnojKomissiiRel.fiz_lico].otchestvo;
            if (confirm('Выдействительно хотите расподписать оценки ' + fio + '?')) {
                briop_ajax({
                    url: '/rukovoditel-komissii/unsign-otsenki',
                    data: {
                        id: item.id
                    },
                    done: function (response) {
                        if (response.type != 'error'){
                            item.status = response.data;
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
        
        rk.getZayavlenie = function (zayavlenieId) {
            console.log(zayavlenieId);
            var id = zayavlenieId;

            briop_ajax({
                url: '/attestaciya/zayavlenie',
                data: {
                    isAjax: 1,
                    id: id
                },
                done: function (data){
                    rk.currentZayavlenieContent = data;
                    rk.hide_zayvlenie = false;
                    $scope.$apply();
                },
            });

            briop_ajax({
                url: '/rukovoditel-komissii/file-zayvleniya',
                data: {
                    id: id
                },
                done: function (response){
                    console.log(response);
                    rk.ispytanieName = response['ispytanie_name'];
                    rk.fileName = response['file_name'];
                    rk.fileLink = response['file_link'];
                    $scope.$apply();
                },
            });
        }

        rk.backToList = function () {
            rk.hide_zayvlenie = true;
            rk.currentZayavlenieContent = '';
            rk.ispytanieName = '';
            rk.fileName = '';
            rk.fileLink = '';
        }

    });

});


$(function(){
    change_url();
})

function change_url(){
    var vp = $('#periods option:selected').val();
    var komissiya = $('#komissiya option:selected').val();
    var params = [];
    if (vp){
        params.push('period=' + vp);
    }
    if (komissiya){
        params.push('komissiya=' + komissiya);
    }
    var link = $('#report_btn').data('link');
    var url = link + '?' + params.join('&');
    $('#report_btn').attr('href',url);
    //window.open(url, '_blank');
}