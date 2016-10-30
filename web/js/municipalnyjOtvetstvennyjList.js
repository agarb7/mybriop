var app = angular.module('list',[]);

app.controller('MOListController', function($scope){
    var mol = this;

    mol.period = $('#periods option:selected').val();

    mol.list = [];

    mol.loadZayavleniya = function(){
        briop_ajax({
            url: '/municipanyj-otvetstvennyj/get-zayavleniya',
            data: {
                period: mol.period
            },
            done: function(response){
                if (response.type == 'success'){
                    mol.list = response.data;
                    $scope.$apply();
                }
            }
        });
    }
});