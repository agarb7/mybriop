var app = angular.module('loss',[]);

app.controller('LossController',function($scope,$rootScope,$http) {
    var l = this;

    l.vp = $('#periods option:first').val();
    l.dolzhnosti = [];

    l.loss = function(){
        l.dolzhnosti = [];
        briop_ajax({
            url: '/attestaciya-otchety/list/loss-dolzhnosti',
            data: {
                vp: l.vp
            },
            done: function (data) {
                console.log(data);
                if (data.type == 'success'){
                    l.dolzhnosti = data.data;
                }
                else{
                    bsalert(data.msg, 'error');
                }
            },
            finally: function(){
                $scope.$apply();
            }
        });
    }

});