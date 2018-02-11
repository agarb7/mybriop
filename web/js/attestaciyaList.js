var otkloneniyaList = [];


$(function(){

    briop_ajax({
        url: '/attestaciya/get-otkloneniya-attestacii',
        data:{

        },
        done: function(data){
            otkloneniyaList = data;
        }
    })

    $('.dolzhnost-btn').click(function(){
        var fizlico = $(this).data('fizlico');
        var zayavlenie = $(this).data('id');
        briop_ajax({
            url: '/attestaciya/add-dolzhnost/',
            data: {
                isAjax: 1,
                fizLicoId: fizlico,
                zayavlenie: zayavlenie,
                list: true
            },
            done: function (data){
                $('#zayavlenie').animate({left: '0'}, 200);
                $('#back-btn').removeClass('hidden');
                $('#zayavlenie-content').html(data);
                $('#lst_content').addClass('hidden');
                $(document).on('submit','#dolzhnostForm',function (e){
                    e.preventDefault();
                    var form = $(this);
                    if ($(form).find('has-error').length>0){
                        return false;
                    }
                    briop_ajax({
                        url: '/attestaciya/submit-add-dolzhnost-zayavleniya/',
                        data: form.serialize(),
                        done: function (answer){
                            if (answer.result == true){
                                bsalert('Должность добавлена');
                                setTimeout(function(){
                                    window.location.href =  '/attestaciya/list?AttestaciyaSpisokFilter%5BzayavlenieId%5D=' + zayavlenie;
                                },1000)
                            }
                            else{
                                $('#zayavlenie-content').html(data);
                            }
                        }
                    });
                });
            }
        });
    });

    $('.more-btn').click(function(){
        var id = $(this).data('id');
        briop_ajax({
            url: '/attestaciya/zayavlenie',
            data: {
                isAjax: 1,
                id: id
            },
            done: function (data){
                $('#zayavlenie').animate({left: '0'}, 200);
                $('#back-btn').removeClass('hidden');
                $('#zayavlenie-content').html(data);
                $('#lst_content').addClass('hidden');
            }
        });
    })

    $('#back-btn').click(function(){
        $('#zayavlenie').animate({left: '100%'}, 200,function(){
            $('#back-btn').addClass('hidden');
            $('#zayavlenie-content').html('');
            $('#lst_content').removeClass('hidden');
        });
    });

    $('.accept-btn').click(function(){
        var fio = $(this).data('fio');
        if (confirm('Вы действительно хотите подтвердить заявление поданное '+fio+'?')) {
            var id = $(this).data('id');
            var parent = $('#tools' + id);
            briop_ajax({
                url: '/attestaciya/accept-zayavlenie',
                data: {
                    isAjax: 1,
                    q: id
                },
                done: function (data) {
                    if (data.result == 'success') {
                        bsalert('Подтверждение успешно выполнено');
                        parent.find('.accept-btn').addClass('hidden');
                        parent.find('.refuse-btn').removeClass('hidden');
                        parent.find('.move-btn').removeClass('hidden');
                        parent.find('.lock-btn').removeClass('hidden');
                        parent.find('.delete-btn').addClass('hidden');
                        parent.find('.achievement-btn').removeClass('hidden');
                        parent.find('.dolzhnost-btn').removeClass('hidden');
                        parent.find('.print-btn').removeClass('hidden');
                        parent.parent().attr('class', 'info');
                    }
                    else
                        bsalert('Подтверждение не выполнено! Ошибка обращения к серверу', 'danger');
                },
                finally: function () {
                    //$('#accept-refuse').click();
                }
            });
        }

    });
/*
    $('.cancel-btn').click(function(){
        var id = $(this).data('id');
        var parent = $(this).parent();
        briop_ajax({
            url: '/attestaciya/cancel-acceptance-zayavelnie',
            data: {
                isAjax: 1,
                q: id
            },
            done: function (data){
                if (data.result == 'success'){
                    bsalert('Отмена подтверждения успешно выполнена');
                    parent.find('.accept-btn').removeClass('hidden');
                    parent.find('.refuse-btn').removeClass('hidden');
                    parent.find('.move-btn').removeClass('hidden');
                    parent.find('.cancel-btn').addClass('hidden');
                    parent.parent().removeClass('info');
                }
                else
                    bsalert('Отмена подтверждения не выполнена! Ошибка обращения к серверу','danger');
            }
        });
    });
*/
    $('.lock-btn').click(function() {
        var id = $(this).data('id');
        var parent = $(this).parent();
        if (confirm('Вы действительно хотите заблокировать заявление? При блокировке заявления будут удалены распределение руководителя аттестационной комиссии, оценочные листы!!!')) {
            briop_ajax({
                url: '/attestaciya/lock-zayavelnie',
                data: {
                    isAjax: 1,
                    q: id
                },
                done: function (data) {
                    if (data.result == 'success') {
                        bsalert('Блокировка заявления успешно выполнена');
                        parent.find('.accept-btn').removeClass('hidden');
                        parent.find('.refuse-btn').addClass('hidden');
                        parent.find('.move-btn').addClass('hidden');
                        parent.find('.lock-btn').addClass('hidden');
                        parent.find('.delete-btn').removeClass('hidden');
                        parent.find('.achievement-btn').addClass('hidden');
                        parent.find('.dolzhnost-btn').addClass('hidden');
                        parent.find('.print-btn').addClass('hidden');
                        parent.parent().attr('class', 'danger');
                    }
                    else
                        bsalert('Блокировка заявления не выполнена! Ошибка обращения к серверу', 'danger');
                }
            });
        }
    });

    $('.refuse-btn').click(function(){
        var id = $(this).data('id');
        var parent = $(this).parent();
        var offset = $(this).offset();
        offset.left = offset.left - 325;
        $('#cancel-buble').removeClass('hidden').offset({left:offset.left, top:offset.top});
        $('#ozid').val(id);
        $('#cancel-buble textarea').focus();
    });

    $('.delete-btn').click(function(){
        var id = $(this).data('id');
        var fio = $(this).data('fio');
        if (confirm('Вы действительно хотите удалить заявление ' + fio + ' (номер заявления ' + id + ')?')){
            var parent = $(this).parent();
            briop_ajax({
                url: '/attestaciya/delete-zayavelnie',
                data:{
                    isAjax: 1,
                    id: id
                },
                done: function(response){
                    if (response.type == 'success'){
                        parent.parent().remove();
                        bsalert(response.msg);
                    }
                    else{
                        bsalert(response.msg, 'danger');
                    }
                },
            });
        }
    });

    $('#cancel-refuse').click(function(){
        $('#cancel-buble textarea').val('');
        $('#cancel-buble').addClass('hidden');
    });

    $('#accept-refuse').click(function(){
        $('#accept-buble textarea').val('');
        $('#accept-buble').addClass('hidden');
    });

    $('#rst-btn').click(function(){
        //$('#filters form')[0].reset();
        //$('#attestaciyaspisokfilter-podtverzhdenieregistracii').prop('checked',false);
        //$('#filters form').submit();
    });

    $('.move-btn').click(function(){
        var id = $(this).data('id');
        var vremya = $(this).data('vremya');
        var parent = $(this).parent();
        var offset = $(this).offset();
        offset.left = offset.left -546;
        offset.top = offset.top -50;
        var option = $('#vremya_provedeniya option:eq('+ vremya +')');
        $('#vremya_provedeniya').val(option.val());
        //console.log($('#vremya_provedeniya').val());
        //$('#vremya_provedeniya option:selected').removeAttr('selected').next().attr('selected', 'selected');
        $('#change_period_buble').removeClass('hidden').offset({left:offset.left, top:offset.top});
        $('#acid').val(id);
    });


    $('.unknown-post-label').click(function(){
        $this = $(this);
        $('#district_names').val(-1);
        var adresnyjObjeKtId = $this.data('ao');
        var organizaciyaId = $this.data('organizaciyaId');
        var currentName = adresnyjObjeKtId == -1 ? 'Не задано' : $('#district_names option[value="' + adresnyjObjeKtId + '"]').text();
        if (adresnyjObjeKtId != -1){
            $('#district_names').val(adresnyjObjeKtId);
        }
        $('#current_organizaciya_id').val(organizaciyaId);
        $('#current_district').text(currentName);
        var parent = $this.parent();
        var offset = $this.position();

        offset.left = offset.left - 325;
        $('#change_district_bubble').appendTo(parent).fadeIn(400);
        $('#district_names').focus();
    });

    function hideDistrictBubble(){
        $('#current_organizaciya_id').val('');
        $('#current_district').text('');
        $('#change_district_bubble').fadeOut(400);
    }

    $('#cancel-district-btn').click(function(){
        hideDistrictBubble();
    });

    $('#update-district-btn').click(function(){
        var districtId = $('#district_names').val();
        if (districtId != -1) {
            var organizaciyaId = $('#current_organizaciya_id').val();
            briop_ajax({
                url: '/attestaciya/update-organizaciya-district',
                data: {
                    organizaciya_id: organizaciyaId,
                    district_id: districtId,
                },
                done: function(data){
                    if (data.type == 'success'){
//                        $('.dolzhnost' + organizaciyaId).off('click');
                        $('.dolzhnost' + organizaciyaId).data('ao', districtId);
                        $('.dolzhnost' + organizaciyaId).removeClass('label label-danger label90 wr-label');
                        hideDistrictBubble();
                        bsalert(data.msg,'success');
                    }
                    else{
                        bsalert(data.msg,'error');
                    }
                }
            })
        }
        //action
    });
});

function close_vremya_form(){
    $('#change_period_buble').addClass('hidden');
    $('#acid').val('');
    //$('#vremya_provedeniya').val($('#vremya_provedeniya option:first').val());
}

function changeVremya(){
    var id = $('#acid').val();
    var vremyaId = $('#vremya_provedeniya option:selected').val();
    var parent = $('#vremya_btn'+id).parent();
    briop_ajax({
        url: '/attestaciya/change-vremya-provedeniya',
        data: {
            id: id,
            vremya_id: vremyaId
        },
        done: function(response){
            if (response.type == 'success'){
                $('#vremya_btn'+id).data('vremya',vremyaId);
                parent.parent().attr('class', 'info');
                bsalert(response.msg,'success');
            }
            else{
                bsalert(response.msg,'danger');
            }
        },
        finally: function(){
            close_vremya_form();
        }
    })
}

function toggle_filters(){
    $('#filters').slideToggle();
}

function otklonit(){
    var id = $('#ozid').val();
    var comment = $('#cancel-buble textarea').val();
    var parent = $('#tools'+id);
    briop_ajax({
        url: '/attestaciya/otklonit-zayavlenie',
        data: {
            isAjax: 1,
            q: id,
            comment: comment
        },
        done: function (data){
            if (data.result == 'success'){
                bsalert('Отклонение для доработки успешно выполнено');
                parent.find('.accept-btn').removeClass('hidden');
                parent.find('.refuse-btn').addClass('hidden');
                parent.find('.move-btn').addClass('hidden');
                parent.find('.lock-btn').addClass('hidden');
                parent.find('.delete-btn').addClass('hidden');
                parent.find('.achievement-btn').addClass('hidden');
                parent.find('.dolzhnost-btn').addClass('hidden');
                parent.find('.print-btn').addClass('hidden');
                parent.parent().addClass('warning');
            }
            else
                bsalert('Отклонение для доработки не выполнено! Ошибка обращения к серверу','danger');
        },
        finally: function(){
            $('#cancel-refuse').click();
        }
    });
}

//function podverdit(){
//    var id = $('#acid').val();
//    var parent = $('#tools'+id);
//    if (!$('#accept_s').val()){
//        bsalert('Введите дату начала испытаний','warning');
//        return;
//    }
//    if (!$('#accept_po').val()){
//        bsalert('Введите дату окончания испытаний','warning');
//        return;
//    }
//    briop_ajax({
//        url: '/attestaciya/accept-zayavlenie',
//        data: {
//            isAjax: 1,
//            q: id,
//            date_s: $('#accept_s').val(),
//            date_po: $('#accept_po').val()
//        },
//        done: function (data){
//            if (data.result == 'success'){
//                bsalert('Подтверждение успешно выполнено');
//                parent.find('.accept-btn').addClass('hidden');
//                parent.find('.refuse-btn').addClass('hidden');
//                parent.find('.cancel-btn').removeClass('hidden');
//                parent.parent().attr('class','info');
//            }
//            else
//                bsalert('Подтверждение не выполнено! Ошибка обращения к серверу','danger');
//        },
//        finally: function(){
//            $('#accept-refuse').click();
//        }
//    });
//}

function changeOtklonenieTip(){
    var tip = $('#otklonenie_tip').val();
    if (tip!=-1 && otkloneniyaList[tip]){
        $('#otklonenie_comment').val(otkloneniyaList[tip]);
    }
    else if (tip =-1){
        $('#otklonenie_comment').val('');
    }
}

