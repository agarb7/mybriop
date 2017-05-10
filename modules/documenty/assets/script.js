/**
 * Created by asv on 31.03.2017.
 */

$(function(){
    $('.podpisanie-btn').click(function(){
        var procid = $(this).data('procid');
        var dokid = $(this).data('dokid');
        var offset = $(this).offset();
        offset.left = offset.left - 325;
        $('#podpisanie').removeClass('hidden').offset({left:offset.left, top:offset.top});
        $('#process_id').val(procid);
        $('#dok_id').val(dokid);
        $('#podpisanie textarea').focus();
    });
    $('#podpisanie_cancel').click(function(){
        $('#podpisanie').addClass('hidden');
        $('#podpisanie textarea').val('');
        $('#process_id').val('');
        $('#dok_id').val('');
    });

    $('.vozvrat-btn').click(function(){
        var procid = $(this).data('procid');
        var dokid = $(this).data('dokid');
        var offset = $(this).offset();
        offset.left = offset.left - 325;
        $('#vozvrat').removeClass('hidden').offset({left:offset.left, top:offset.top});
        $('#voz_process_id').val(procid);
        $('#voz_dok_id').val(dokid);
        $('#vozvrat textarea').focus();
    });
    $('#vozvrat_cancel').click(function(){
        $('#vozvrat').addClass('hidden');
        $('#vozvrat textarea').val('');
        $('#voz_process_id').val('');
        $('#voz_dok_id').val('');
    });

    $('.registracija-btn').click(function(){
        var procid = $(this).data('procid');
        var dokid = $(this).data('dokid');
        var offset = $(this).offset();
        offset.left = offset.left - 325;
        $('#registracija').removeClass('hidden').offset({left:offset.left, top:offset.top});
        $('#reg_process_id').val(procid);
        $('#reg_dok_id').val(dokid);
        $('#nomer_reg').focus();
    });
    $('#registracija_cancel').click(function(){
        $('#registracija').addClass('hidden');
        $('#nomer_reg').val('');
        $('#date_reg').val('');
        $('#reg_process_id').val('');
        $('#reg_dok_id').val('');
    });
    
    $('.edit-btn').click(function(){
        var procid = $(this).data('procid');
        var dokid = $(this).data('dokid');
        
    });
});

function podpisanie(){
    var procid = $('#process_id').val();
    var dokid = $('#dok_id').val();
    var comment = $('#podpisanie textarea').val();
    briop_ajax({
        url: '/documenty/process/podpisanie',
        data: {
            isAjax: 1,
            procid: procid,
            comment: comment
        },
        done: function (data){
            if (data.result == 'success'){
                bsalert('Документ успешно подписан!');
                $('tr[data-key='+dokid+']').remove();
            }
            else
                bsalert('Документ не подписан! Ошибка обращения к серверу','danger');
        },
        finally: function(){
            $('#podpisanie_cancel').click();
        }
    });
}

function vozvrat(){
    var procid = $('#voz_process_id').val();
    var dokid = $('#voz_dok_id').val();
    var comment = $('#vozvrat textarea').val();
    briop_ajax({
        url: '/documenty/process/vozvrat',
        data: {
            isAjax: 1,
            procid: procid,
            comment: comment
        },
        done: function (data){
            if (data.result == 'success'){
                bsalert('Документ успешно возвращен автору на доработку!');
                $('tr[data-key='+dokid+']').remove();
            }
            else
                bsalert('Документ не возвращен! Ошибка обращения к серверу','danger');
        },
        finally: function(){
            $('#vozvrat_cancel').click();
        }
    });
}

function registracija(){
    var procid = $('#reg_process_id').val();
    var dokid = $('#reg_dok_id').val();
    var nomer = $('#nomer_reg').val();
    var datereg = $('#date_reg').val();
    briop_ajax({
        url: '/documenty/process/registracija',
        data: {
            isAjax: 1,
            procid: procid,
            nomer: nomer,
            datereg: datereg,
        },
        done: function (data){
            if (data.result == 'success'){
                bsalert('Документ успешно зарегестрирован!');
                $('tr[data-key='+dokid+']').remove();
            }
            else
                bsalert('Документ не зарегестрирован! Ошибка обращения к серверу','danger');
        },
        finally: function(){
            $('#registracija_cancel').click();
        }
    });
}
