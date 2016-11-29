var app = angular.module('othect',[]);

app.controller('OtchetController',function($scope,$rootScope,$http) {
    var o = this;

    o.vp = $('#periods option:first').val();
    o.komissiya = $('#komissiya option:first').val();
    o.sotrudniki = [];

    o.load = function(){
        o.sotrudniki = [];
        briop_ajax({
            url: '/attestaciya-otchety/list/sotrudnik-komissii',
            data: {
                vp: o.vp,
                komissiya: o.komissiya
            },
            done: function (data) {
                if (data.type == 'success'){
                    o.sotrudniki = data.data;
                }
                else{
                    bsalert(data.msg, 'error');
                }
            },
            finally: function(){
                $scope.$apply();
            }
        })

    };

});