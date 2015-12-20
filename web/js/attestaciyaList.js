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

        var id = $(this).data('id');
        var parent = $(this).parent();
        var offset = $(this).offset();
        offset.left = offset.left - 215;
        $('#accept-buble').removeClass('hidden').offset({left:offset.left, top:offset.top});
        $('#acid').val(id);



    });

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
                    parent.find('.cancel-btn').addClass('hidden');
                    parent.parent().removeClass('info');
                }
                else
                    bsalert('Отмена подтверждения не выполнена! Ошибка обращения к серверу','danger');
            }
        });
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

    $('#cancel-refuse').click(function(){
        $('#cancel-buble textarea').val('');
        $('#cancel-buble').addClass('hidden');
    });

    $('#accept-refuse').click(function(){
        $('#accept-buble textarea').val('');
        $('#accept-buble').addClass('hidden');
    });

    $('#rst-btn').click(function(){
        $('#filters form')[0].reset();
        $('#attestaciyaspisokfilter-podtverzhdenieregistracii').prop('checked',false);
        $('#filters form').submit();
    });

});

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
                bsalert('Отклонение заявления успешно выполнено');
                parent.find('.accept-btn').removeClass('hidden');
                parent.find('.refuse-btn').addClass('hidden');
                parent.find('.cancel-btn').addClass('hidden');
                parent.parent().addClass('danger');
            }
            else
                bsalert('Отклонение заявления не выполнено! Ошибка обращения к серверу','danger');
        },
        finally: function(){
            $('#cancel-refuse').click();
        }
    });
}

function podverdit(){
    var id = $('#acid').val();
    var parent = $('#tools'+id);
    if (!$('#accept_s').val()){
        bsalert('Введите дату начала испытаний','warning');
        return;
    }
    if (!$('#accept_po').val()){
        bsalert('Введите дату окончания испытаний','warning');
        return;
    }
    briop_ajax({
        url: '/attestaciya/accept-zayavlenie',
        data: {
            isAjax: 1,
            q: id,
            date_s: $('#accept_s').val(),
            date_po: $('#accept_po').val()
        },
        done: function (data){
            if (data.result == 'success'){
                bsalert('Подтверждение успешно выполнено');
                parent.find('.accept-btn').addClass('hidden');
                parent.find('.refuse-btn').addClass('hidden');
                parent.find('.cancel-btn').removeClass('hidden');
                parent.parent().attr('class','info');
            }
            else
                bsalert('Подтверждение не выполнено! Ошибка обращения к серверу','danger');
        },
        finally: function(){
            $('#accept-refuse').click();
        }
    });
}

function changeOtklonenieTip(){
    var tip = $('#otklonenie_tip').select2('val');
    if (tip!=-1 && otkloneniyaList[tip]){
        $('#otklonenie_comment').val(otkloneniyaList[tip]);
    }
    else if (tip =-1){
        $('#otklonenie_comment').val('');
    }
}

