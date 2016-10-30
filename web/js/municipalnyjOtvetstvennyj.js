var app = angular.module('mo', []);

app.controller('MunicipalnyjOtevetstvennyjController', function($scope,$rootScope){
    var mo = this;

    mo.data = [];
    mo.getData = function(){
        briop_ajax({
            url: '/municipanyj-otvetstvennyj/get-districts',
            done: function(response){
                if (response.type == 'success') {
                    mo.data = response.data
                    $scope.$apply();
                }
            }
        })
    }
    this.getData();

    mo.chosenFizLico = null;
    mo.currentDistrict = null;

    mo.chooseMo = function(){
        briop_ajax({
            url: '/municipanyj-otvetstvennyj/set-municipalnogo-otvestvennogo',
            data: {
                district_id: mo.currentDistrict.id,
                fiz_lico: mo.chosenFizLico
            },
            done: function(response){
                if (response.type == 'success'){
                    bsalert(response.msg, 'success');
                    mo.chosenFizLico = null;
                    mo.getData();
                    $('#moModal').modal('toggle');
                }
                else{
                    bsalert(response.msg, 'danger');
                }
            }
        });
    }

    mo.setCurrentDistrict = function(district){
        mo.currentDistrict = district;
    }

})