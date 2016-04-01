var app = angular.module('otsenki',[]);

app.controller('SpisokController',function($scope, $rootScope){
    var s = this;
    s.period = $('#periods option:selected').val();

    s.spisok = [];

    s.loadZayavleniya = function(){
        briop_ajax({
            url: '/sotrudnik-att-komissii/get-zayvleniya',
            data: {
                period_id: s.period
            },
            done: function(response){
                s.spisok = response.data;
                $scope.$apply();
            },
        });
    }

    s.putMarks = function(zayavlenieId){
        $rootScope.$broadcast('otsenki', zayavlenieId);
    }
});

app.controller('OtsenkiController', function($scope, $rootScope){
    var o = this;

    o.zayavlenieId = -1;

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
                    bsalert(response.msg,'success');
                }
                else{
                    bsalert(response.msg,'danger');
                }
            }
        });
    });
});
