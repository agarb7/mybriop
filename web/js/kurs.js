$(document).ready(function(){

    $('#smb_btn').click(function(){
       //$('button[type=submit]').remo
        //$(this).attr('clicked','true');
        $('#kursModel-form').submit();
    });
});


function recalculculate_order_num(){
    var razdel_num = 0;
    var podrazdel_num = 0;
    var theme_num = 0;
    $('.numbered').each(function(){
        if ($(this).hasClass('razdel-row')) {
            var next_element = $(this).next();
            if (!$(next_element).hasClass(' section_footer_razdel'))
                razdel_num++;
            podrazdel_num=0;
            theme_num = 0
            //console.log(razdel_num);
        }
        if ($(this).hasClass('podrazdel-row')) {
            podrazdel_num++;
            theme_num=0;
            $(this).find('.num').text(razdel_num+'.'+podrazdel_num);
            //console.log(razdel_num+'.'+podrazdel_num);
        }
        if ($(this).hasClass('theme-row')) {
            theme_num++;
            $(this).find('.num').text(razdel_num+'.'+podrazdel_num+'.'+theme_num);
            //console.log();
        }
    });
}

function razdel_cmb_change(id, num){
    var cur_value = $('#'+id+' option:selected').val();
    if (cur_value == -1){
        $('#add_razdel_nazvanie'+num).removeClass('hidden');
    }
    else{
        $('#add_razdel_nazvanie'+num).addClass('hidden');
    }
}

function add_nazvanie_razdela_to_cmb(id,nazvanie){
    //var option = document.createElement('option');
    //option.value = id;
    //option.text = nazvanie;
    var html = '<option value="'+id+'">'+nazvanie+'</option>';
    $('#razdel_nazvanie').append(html);
    $('#edit_razdel_nazvanie').append(html);
}

function set_podpis(kurs_id,is_checked){
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'check_kurs',
            kurs_id:kurs_id,
            is_checked:is_checked ? 1 : 0
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {
                $('#status_programmy').prop('checked',is_checked);
            }
            else {
                //$('#status_programmy').prop('checked',is_checked);
                show_msg('danger', data.msg,7000);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger', 'Ошибка выполнения ajax-запроса!');
        }
    });
}

function isEven (someNumber) {
    return (someNumber % 2 == 0) ? true : false;
};

$(function(){
    recalculculate_order_num();
})

$(document).ajaxSuccess(function(event, request, settings){
    $('.loader').addClass('hidden');
    var r = JSON.parse(request.responseText);
    if (r.hasOwnProperty('files')) return;
    if (!r.hasOwnProperty('is_set_podpis')) {
        if (r.res != 'error') {
            show_msg('success', 'Изменения успешно выполнены');
            var kurs_id = $('#razdel_kurs_id').val();
            set_podpis(kurs_id,false);
        }
    }
});

$(document).ajaxError(function(){
    $('.loader').addClass('hidden');
});

$(document).ajaxStart(function(){
    $('.loader').removeClass('hidden');
});

function hide_form(form_id){
    $('#'+form_id).addClass('hidden');
}

function show_msg(type,msg,delay){ //type - success,info,warning,danger
    if (delay == undefined) delay=3000;
    $('#alert-div').removeClass('alert-success alert-info alert-warning alert-danger');
    $('#alert-div').addClass('alert-'+type);
    $('#alert-div').html(msg);
    $('#alert-div').fadeIn(600).delay(delay).fadeOut(600);
}

function move_razdel_to_other_tip(razdel_id, tip){
    var tr = $('#razdel'+razdel_id);
    var html = '';
    while(true){
        html += $(tr)[0].outerHTML;
        var old_tr = tr;
        if ($(tr).hasClass('section_footer_razdel')) {
            $(old_tr).remove();
            break;
        }
        tr = $(tr).next('tr');
        $(old_tr).remove();
    }
    $(html).insertBefore($('#'+tip+'_tr_footer'));
    console.log(html);
}

function add_razdel(){
    $('#razdel_nazvanie').val($('#razdel_nazvanie option:first').val());
    $('#add_razdel_form').removeClass('hidden');
    $('#razdel_types option:selected').val($('#razdel_types option:first').val( ));
    $('#add_razdel_nazvanie1').addClass('hidden');
    $('#new_razdel_nazvanie').val("");
    $('#razdel_nazvanie').focus();

}

function save_razdel(){
    var kurs_id = $('#razdel_kurs_id').val();
    var nazvanie = $('#razdel_nazvanie option:selected').val();
    var type = $('#razdel_types option:selected').val();
    var new_nazvanie = $('#new_razdel_nazvanie').val();
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'add_razdel',
            kurs_id:kurs_id,
            nazvanie:nazvanie,
            type: type,
            new_nazvanie: new_nazvanie
        },
        success: function( data ) {
            console.log(data);
            if (data.res != 'error') {
                var selector = '';
                if (type=='baz') selector = 'baz_tr_footer';
                else selector = 'prof_tr_footer';
                $(data.html).insertBefore($('#'+selector));
                hide_form('add_razdel_form');
                if (nazvanie == '-1'){
                    add_nazvanie_razdela_to_cmb(data.nazvanie,new_nazvanie);
                }
            }
            else{
                show_msg('danger',data.msg);
            }
        },
        error: function (e,t){
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function edit_razdel(razdel_id){
    var nazvaie_id = $('#razdel_nazvanie_id'+razdel_id).val();
    var type = $('#razdel_tip'+razdel_id).val();
    $('#new_edit_razdel_nazvanie').val('');
    $('#add_razdel_nazvanie2').addClass('hidden');
    $('#edit_razdel_nazvanie').val(nazvaie_id);
    $('#edit_razdel_id').val(razdel_id);
    $('#edit_razdel_types').val(type);
    var offset = $('#razdel'+razdel_id+' .data').offset();
    var offset_height = $('#razdel'+razdel_id+' .data').height();
    offset.top = offset.top + offset_height+10;
    offset.left = offset.left + 10;
    $('#edit_razdel_form').removeClass('hidden');
    $('#edit_razdel_form').offset({top:offset.top, left: offset.left});
    $('#edit_razdel_nazvanie').focus();
}

function save_edit_razdel(){
    var razdel_id = $('#edit_razdel_id').val();
    var nazvanie_id =$('#edit_razdel_nazvanie option:selected').val();
    var old_nazvanie_id = $('#razdel_nazvanie_id'+razdel_id).val();
    var type = $('#edit_razdel_types option:selected').val();
    var old_type = $('#razdel_tip'+razdel_id).val();
    var kurs_id = $('#razdel_kurs_id').val();
    var new_nazvanie = $('#new_edit_razdel_nazvanie').val();
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'edit_razdel',
            razdel_id: razdel_id,
            nazvanie_id:nazvanie_id,
            old_nazvanie_id:old_nazvanie_id,
            kurs_id: kurs_id,
            type: type,
            new_nazvanie: new_nazvanie
        },
        success: function( data ) {
            console.log(data);
            if (data.res != 'error') {
                //$('#topics_table').append(data.html);
                $('#razdel'+razdel_id).replaceWith(data.html);
                if (old_type!=type) move_razdel_to_other_tip(razdel_id,type);
                hide_form('edit_razdel_form');
                if (nazvanie_id == '-1'){
                    add_nazvanie_razdela_to_cmb(data.nazvanie,new_nazvanie);
                }
            }
            else{
                show_msg('danger',data.msg);
            }
        },
        error: function (e,t){
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function delete_razdel(razdel_id){
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_razdel',
            razdel_id:razdel_id
        },
        success: function( data ) {
            console.log(data);
            if (data.res != 'error') {
                $('#section_footer_'+razdel_id).remove();
                $('#razdel'+razdel_id).remove();
            }
            else{
                show_msg('danger',data.msg);
            }
        },
        error: function (e,t){
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function add_podrazdel(num){
    $('#razdel'+num+" .data").append($('#add_podrazdel_form'));
    $('#podrazdel_name').val('');
    $('#rukovoditel_podrazdela').val($('#rukovoditel_podrazdela option:first').val());
    $('#podrazdel_lk').val('');
    $('#podrazdel_pr').val('');
    $('#podrazdel_srs').val('');
    $('#podrazdel_fk').val($('#podrazdel_fk option:first').val());
    $('#razdel').val(num);
    $('#podrazdel_nedelya_nachalo').val(1);
    $('#podrazdel_nedelya_konec').val(1);
    $('#add_podrazdel_form').removeClass('hidden');
    $('#podrazdel_fk_chasy').val('');
    $('#podrazdel_name').focus();
}

function get_last_num_of_podrazdels(razdel_id){
    var podrazdel = $('.podrazdel'+razdel_id).last();
    if (podrazdel.length>0) {
        var number = $(podrazdel).find('.podrazdel_nomer');
        return parseInt($(number).val());
    }
    else{
        return 0;
    }
}

function save_podrazdel(){
    var kurs_type = $('#kurs_type').val();
    var name = $('#podrazdel_name').val();
    var razdel = $('#razdel').val();
    var rukovoditel = kurs_type == 'pk' ? undefined : $('#rukovoditel_podrazdela option:selected').val();
    var lk = kurs_type == 'pk' ? undefined : $('#podrazdel_lk').val();
    var pr = kurs_type == 'pk' ? undefined : $('#podrazdel_pr').val();
    var srs = kurs_type == 'pk' ? undefined : $('#podrazdel_srs').val();
    var fk = kurs_type == 'pk' ? undefined : $('#podrazdel_fk option:selected').val();
    var nomer = get_last_num_of_podrazdels(razdel)+1;
    var errors = '';
    var nedelya_nachalo = $('#podrazdel_nedelya_nachalo option:selected').val();
    var nedelya_konec = $('#podrazdel_nedelya_konec option:selected').val();
    var chasy_kontrolya = $('#podrazdel_fk_chasy').val();
    if (kurs_type!='pk'){
        if (!(isEven(lk) && is_positive_interger(lk))) errors +='<p>Количество лекционных часов должно быть целым положительным числом кратным двум</p>';
        //if (!(isEven(pr) && is_positive_interger(pr))) errors +='<p>Количество практических часов должно быть целым положительным числом кратным двум</p>';
        //if (!is_positive_interger(srs)) errors +='<p>Количество часов СРС должно быть целым положительным числом</p>';
        if (nedelya_konec < nedelya_nachalo) errors += '<p>Номер последней недели не должен быть меньше номера первой недели</p>';
        if (!chasy_kontrolya) errors += '<p>Введите часы контроля</p>';
    }
    if (errors != '')
        show_msg('danger',errors,3000);
    else if (name){

        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'add_podrazdel',
                name:name,
                razdel:razdel,
                nomer: nomer,
                rukovoditel: rukovoditel,
                kurs_type: kurs_type,
                lk: lk,
                pr: pr,
                srs: srs,
                fk: fk,
                nedelya_nachalo: nedelya_nachalo,
                nedelya_konec: nedelya_konec,
                chasy_kontrolya: chasy_kontrolya
            },
            success: function( data ) {
                console.log(data);
                if (data.res != 'error') {
                    $(data.html).insertBefore($('#section_footer_'+razdel));
                    hide_form('add_podrazdel_form');
                    recalculculate_order_num();
                }
                else{
                    show_msg('danger','Ошибка! Изменения не сохранены');
                }
            },
            error: function (e,t){
                console.log(e.responseText);
                show_msg('danger','Ошибка выполнения ajax-запроса!');
            }
        });
    }
}

function edit_podrazdel(id){
    $('#edit_podrazdel_id').val(id);
    $('#edit_podrazdel_name').val($('#podrazdel_name'+id).text());
    $('#podrazdel'+id+' .data').append($('#edit_podrazdel_form'));
    $('#edit_rukovoditel_podrazdela').val($('#rp'+id).val() || 1);
    $('#edit_podrazdel_lk').val($('#podrazdel_lk'+id).text());
    $('#edit_podrazdel_pr').val($('#podrazdel_pr'+id).text());
    $('#edit_podrazdel_srs').val($('#podrazdel_srs'+id).text());
    $('#edit_podrazdel_fk').val($('#kf_podrazdel'+id).val() || 1);
    $('#edit_podrazdel_nedelya_nachalo').val($('#nedelya_nachalo'+id).val() || 1);
    $('#edit_podrazdel_nedelya_konec').val($('#nedelya_konec'+id).val() || 1);
    $('#edit_podrazdel_fk_chasy').val($('#chasy_kf_podrazdela'+id).val());
    $('#edit_podrazdel_form').removeClass('hidden');
    $('#edit_podrazdel_name').focus();
}

function save_edit_podrazdel(){
    var name = $('#edit_podrazdel_name').val();
    var podrazdel_id = $('#edit_podrazdel_id').val();
    var kurs_type = $('#kurs_type').val();
    var rukovoditel = kurs_type == 'pk' ? undefined : $('#edit_rukovoditel_podrazdela option:selected').val();
    var lk = kurs_type == 'pk' ? undefined : $('#edit_podrazdel_lk').val();
    var pr = kurs_type == 'pk' ? undefined : $('#edit_podrazdel_pr').val();
    var srs = kurs_type == 'pk' ? undefined : $('#edit_podrazdel_srs').val();
    var fk = kurs_type == 'pk' ? undefined : $('#edit_podrazdel_fk option:selected').val();
    var nedelya_nachalo = $('#edit_podrazdel_nedelya_nachalo option:selected').val();
    var nedelya_konec = $('#edit_podrazdel_nedelya_konec option:selected').val();
    //var nomer = get_last_num_of_podrazdels(razdel)+1;
    var chasy_kontrolya = $('#edit_podrazdel_fk_chasy').val();
    var errors = '';
    if (kurs_type!='pk'){
        if (!(isEven(lk) && is_positive_interger(lk))) errors +='<p>Количество лекционных часов должно быть целым положительным числом кратным двум</p>';
        if (!(isEven(pr) && is_positive_interger(pr))) errors +='<p>Количество практических часов должно быть целым положительным числом кратным двум</p>';
        if (!is_positive_interger(srs)) errors +='<p>Количество часов СРС должно быть целым положительным числом</p>';
        if (nedelya_konec < nedelya_nachalo) errors += '<p>Номер последней недели не должен быть меньше номера первой недели</p>';
        if (!chasy_kontrolya) errors += '<p>Введите часы контроля</p>';
    }
    if (errors != '')
        show_msg('danger',errors,3000);
    else if (name) {
        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'edit_podrazdel',
                name: name,
                podrazdel_id: podrazdel_id,
                rukovoditel: rukovoditel,
                kurs_type: kurs_type,
                lk: lk,
                pr: pr,
                srs: srs,
                fk: fk,
                nedelya_nachalo: nedelya_nachalo,
                nedelya_konec: nedelya_konec,
                chasy_kontrolya: chasy_kontrolya
            },
            success: function (data) {
                console.log(data);
                if (data.res != 'error') {
                    $('body').append($('#edit_podrazdel_form'));
                    //$('#podrazdel_name' + podrazdel_id).text(name);
                    $('#podrazdel'+podrazdel_id).replaceWith(data.html);
                    hide_form('edit_podrazdel_form');
                    recalculculate_order_num();
                }
                else {
                    show_msg('danger', 'Ошибка! Изменения не сохранены');
                }
            },
            error: function (e, t) {
                console.log(e.responseText);
                show_msg('danger','Ошибка выполнения ajax-запроса!');
            }
        });
    }
}

function delete_podrazdel(id){
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_podrazdel',
            podrazdel_id:id
        },
        success: function( data ) {
            console.log(data);
            if (data.res != 'error') {
                $('#section_footer_podrazdel'+id).remove();
                $('#section_footer_podrazdel_kf'+id).remove();
                $('#podrazdel'+id).remove();
                recalculculate_order_num()
            }
            else{
                show_msg(data.type,data.msg);
            }
        },
        error: function (e,t){
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function add_theme(podrazdel_id){
    $('#podrazdel'+podrazdel_id+" .data").append($('#add_theme_form'));
    $('#theme_chasy').val('');
    $('#soderzhanie').val('');
    $('#theme_name').val('');
    $('#podrazdel_id').val(podrazdel_id);
    $("#vid_rabot").val($("#vid_rabot option:first").val());
    $("#sotrudniki").val($("#sotrudniki option:first").val());
    $("#theme_week").val($('#theme_week option:first').val());
    $('#add_theme_form').removeClass('hidden');
    $('#theme_name').focus();
}

function get_last_num_of_themes(podrazdel_id){
    var theme = $('.theme'+podrazdel_id).last();
    if (theme.length>0) {
        var number = $(theme).find('.theme_nomers');
        return parseInt($(number).val());
    }
    else{
        return 0;
    }
}


function save_theme(){
    var podrazdel_id = $('#podrazdel_id').val();
    var vid_rabot = $('#vid_rabot option:selected').val();
    var sotrudnik = $('#sotrudniki option:selected').val();
    var theme_name = $('#theme_name').val();
    var soderzhanie = $('#soderzhanie').val();
    var chasy = $('#theme_chasy').val();
    var kurs_type = $('#kurs_type').val();
    var nomer = get_last_num_of_themes(podrazdel_id)+1;
    var week = $("#theme_week option:selected").val();
    //if ($('#kurs_type').val() == 'pk') chasy = 2;
    if (theme_name && chasy) {
        if (isEven(chasy)) {
            $.ajax({
                url: "/kurs/ajax",
                type: "POST",
                dataType: "json",
                data: {
                    ajax_query: 'add_theme',
                    podrazdel_id: podrazdel_id,
                    name: theme_name,
                    vid_rabot: vid_rabot,
                    sotrudnik: sotrudnik,
                    soderzhanie: soderzhanie,
                    chasy: chasy,
                    kurs_type: kurs_type,
                    nomer: nomer,
                    week: week
                },
                success: function (data) {
                    console.log(data);
                    if (data.res != 'error') {
                        $(data.html).insertBefore($('#section_footer_podrazdel' + podrazdel_id));
                        hide_form('add_theme_form');
                        recalculculate_order_num();
                    }
                    else {
                        show_msg('danger', data.msg);
                    }
                },
                error: function (e, t) {
                    console.log(e.responseText);
                    show_msg('danger', 'Ошибка выполнения ajax-запроса!');
                }
            });
        }
        else show_msg('warning','Количество часов должно быть кратно 2');
    }
}

function edit_them(theme_id){
 var nazvanie = $('#theme_nazvanie'+theme_id).text();
 var soderzhanie = $('#soderzhanie'+theme_id).text();
 var prepodavatel = $('#prepodavatel'+theme_id).val();
 var vid_rabot = $('#vid_rabot'+theme_id).val();
 var chasy = $('#theme_chasy'+theme_id).text();
 var week = $('#theme_week'+theme_id).text();
 $('#theme_edit_name').val(nazvanie);
 $('#vid_edit_rabot').val(vid_rabot);
 $('#sotrudniki_edit').val(prepodavatel);
 $('#soderzhanie_edit').val(soderzhanie);
 $('#theme_id').val(theme_id);
 $('#theme_edit_chasy').val(chasy);
 $('#theme_edit_week').val(week);
 $('#theme'+theme_id+' .data').append($('#edit_theme_form'));
 $('#edit_theme_form').removeClass('hidden');
}

function save_edit_theme(){
    var theme_id = $('#theme_id').val();
    var nazvanie = $('#theme_edit_name').val();
    var soderzhanie = $('#soderzhanie_edit').val();
    var vid_rabot =$('#vid_edit_rabot option:selected').val();
    var prepodavatel = $('#sotrudniki_edit option:selected').val();
    var chasy = $('#theme_edit_chasy').val();
    var kurs_type = $('#kurs_type').val();
    var week = $('#theme_edit_week option:selected').val();
    //if ($('#kurs_type').val() == 'pk') chasy = 2;
    if (nazvanie && chasy) {
        if (isEven(chasy)) {
            $.ajax({
                url: "/kurs/ajax",
                type: "POST",
                dataType: "json",
                data: {
                    ajax_query: 'save_edit_theme',
                    theme_id: theme_id,
                    nazvanie: nazvanie,
                    soderzhanie: soderzhanie,
                    vid_rabot: vid_rabot,
                    prepodavatel: prepodavatel,
                    chasy: chasy,
                    kurs_type:kurs_type,
                    week: week
                },
                success: function (data) {
                    console.log(data);
                    if (data.res != 'error') {
                        hide_form('edit_theme_form');
                        $('#edit_theme_form').appendTo('body');
                        $('#theme' + theme_id).replaceWith(data.html);
                        recalculculate_order_num();
                    }
                    else {
                        show_msg('danger', data.msg);
                    }
                },
                error: function (e, t) {
                    console.log(e.responseText);
                    show_msg('danger', 'Ошибка выполнения ajax-запроса!');
                }
            });
        }
        else show_msg('warning','Количество часов должно быть кратно 2');
    }
}

function delete_theme(theme_id){
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_theme',
            theme_id: theme_id
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {
                $('#section_footer_theme'+theme_id).remove();
                $('#theme'+theme_id).remove();
                recalculculate_order_num();
            }
            else {
                show_msg(data.type, data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function add_umk(theme_id){
    $('#add_umk_form').removeClass('hidden');
    var offset = $('#theme'+theme_id+' .data').offset();
    var offset_height = $('#theme'+theme_id+' .data').height();

    offset.top = offset.top + offset_height+10;
    offset.left = offset.left + 30;

    $('#add_umk_form').offset({top:offset.top, left: offset.left});


    $('#umk_opisanie').val('');
    $('#umk_theme_id').val(theme_id);
    $("#umk").files2('set_file',-1);
    $('#type_umk').val($('#type_umk option:first').val());
    change_umk_type('umk');
    $('#umk_urlumk').val('');
    //$('#umk_url_blockumk').addClass('hidden');
    //$('#umk_file_blockumk').removeClass('hidden');
    $('#umk_opisanie').focus();
}

function save_umk(){
    var opisanie = $("#umk_opisanie").val();
    var umk_type = $('#type_umk option:selected').val();
    var url = $('#umk_urlumk').val();
    var file = $('#umk').files2('get_file_id');
    var theme_id = $('#umk_theme_id').val();
    var tip_kursa = $('#kurs_type').val();
    var tip = 2;
    if ((umk_type==1 && file=='') || (umk_type==2 && url=='')) return false;
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'add_umk',
            theme_id: theme_id,
            umk_type: umk_type,
            file: file,
            url: url,
            opisanie:opisanie,
            tip_kursa: tip_kursa,
            tip: tip
        },
        success: function (data) {
            console.log(data);
            if (data.res == 'nothing') return false;
            if (data.res != 'error') {
                hide_form('add_umk_form');
                $(data.html).insertBefore($('#section_footer_theme'+theme_id));
            }
            else {
                show_msg(data.type, data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function edit_umk(umk_id){
    $('#edit_umk_form').removeClass('hidden');
    var offset = $('#umk'+umk_id+' .data').offset();
    var offset_height = $('#umk'+umk_id+' .data').height();

    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 30;


    $('#edit_umk_form').offset({top:offset.top, left: offset.left});

    var opisanie = $('#umk_opisanie'+umk_id).text();
    var file_name =$('#umk_file_name'+umk_id).text();
    var file_id = $('#umk_file_id'+umk_id).val();
    var url = $('#url'+umk_id).text();
    var ftype = 1;
    if (url!='') ftype=2;

    $('#type_umk_edit').val(ftype);
    $('#umk_edit_opisanie').val(opisanie);
    change_umk_type('umk_edit');
    $('#umk_edit').files2('set_file',file_id);
    //$('#btnumk_filesumk_edit').text(file_name);
    //$('#umk_filesumk_edit').val(file_id);
    $('#umk_urlumk_edit').val(url);
    //if (ftype == 2){
    //    $('#btnumk_filesumk_edit').text('Выберите файл');
    //    $('#umk_filesumk_edit').val('');
    //}
    $('#umk_edit_opisanie').focus();
    $('#umk_id').val(umk_id);
}

function save_edit_umk(){
    var opisanie = $("#umk_edit_opisanie").val();
    var umk_type = $('#type_umk_edit option:selected').val();
    var url = $('#umk_urlumk_edit').val();
    var file = $('#umk_edit').files2('get_file_id');
    var umk_id = $('#umk_id').val();
    var tip_kursa = $('#kurs_type').val();
    var tip = $('#tip'+umk_id).val();
    if ((umk_type==1 && file=='') || (umk_type==2 && url=='')) return false;
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'edit_umk',
            umk_id: umk_id,
            umk_type: umk_type,
            file: file,
            url: url,
            opisanie:opisanie,
            tip_kursa:tip_kursa,
            tip: tip
        },
        success: function (data) {
            console.log(data);
            if (data.res == 'nothing') return false;
            if (data.res != 'error') {
                hide_form('edit_umk_form');
                $('#umk'+umk_id).replaceWith(data.html);
            }
            else {
                show_msg(data.type, data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function delete_umk(umk_id,tip){
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_umk',
            umk_id: umk_id,
            tip: tip
        },
        success: function (data) {
            console.log(data);
            if (data.res == 'nothing') return false;
            if (data.res != 'error') {
                $('#umk'+umk_id).remove();
            }
            else {
                show_msg(data.type, data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
        }
    });
}

function add_theme_control_form(theme_id){
    $('#theme_kf_id').val(theme_id);
    $('#forma_kontrolya_temi').val($('#forma_kontrolya_temi option:first').val());
    $('#add_cc_form').removeClass('hidden');
    var offset = $('#theme'+theme_id+' .data').offset();
    var offset_height = $('#theme'+theme_id+' .data').height();

    offset.top = offset.top + offset_height+10;
    offset.left = offset.left + 30;

    //$('#forma_kontrolya_temi').focus();
    $('#add_cc_form').offset({top:offset.top, left: offset.left});
}

function save_kf(){
    var theme_id = $('#theme_kf_id').val();
    var forma_kf_id = $('#forma_kontrolya_temi option:selected').val();
    var tip_kursa = $('#kurs_type').val();
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'save_kf',
            theme_id:theme_id,
            forma_kf_id:forma_kf_id,
            tip_kursa:tip_kursa
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {
                $(data.html).insertAfter($('#theme'+theme_id));
                hide_form('add_cc_form');
                $('#add_kf_block'+theme_id).addClass('hidden');
            }
            else {
                show_msg('danger', data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function edit_kf(theme_id){
    $('#theme_kf_edit_id').val(theme_id);
    var kf_id = $('#kf_id'+theme_id).val();
    $('#forma_kontrolya_temi_edit').val(kf_id);
    //$('#')
    $('#add_cc_edit_form').removeClass('hidden');
    var offset = $('#kf'+theme_id+' .data').offset();
    var offset_height = $('#kf'+theme_id+' .data').height();

    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 30;


    $('#add_cc_edit_form').offset({top:offset.top, left: offset.left});
}

function save_edit_kf(){
    var theme_id = $('#theme_kf_edit_id').val();
    var tip_kursa = $('#kurs_type').val();
    var kf_id = $('#forma_kontrolya_temi_edit option:selected').val();
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'save_edit_kf',
            theme_id:theme_id,
            kf_id:kf_id,
            tip_kursa:tip_kursa
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {
                $('#kf'+theme_id).replaceWith(data.html);
                hide_form('add_cc_edit_form');
            }
            else {
                show_msg('danger', 'Форма контроля не изменена');
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function delete_kf(theme_id){

    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_kf',
            theme_id: theme_id
        },
        success: function (data) {
            if (data.res == 'nothing') return false;
            if (data.res != 'error') {
                $('#kf'+theme_id).remove();
                $('#section_footer_kf'+theme_id).remove();
                $('#add_kf_block'+theme_id).removeClass('hidden');
            }
            else {
                show_msg(data.type, data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function add_kim(theme_id){
    $('#theme_kim_id').val(theme_id);
    $('#kim_opisanie').val('');
    $('#type_kim').val(1);
    onchange_kim_type('kim');
    $('#kim').files2('set_file',-1);
    //$('#btnkim_file').text('Выберите файл');
    //$('#kim_file').val('');
    $('#kim_url').val('');
    $('#kim_text').val('');

    $('#add_kim_form').removeClass('hidden');
    var offset = $('#kf'+theme_id+' .data').offset();
    var offset_height = $('#kf'+theme_id+' .data').height();

    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 10;

    $('#add_kim_form').offset({top:offset.top, left: offset.left});
}

function save_kim(){
    var theme_id =$('#theme_kim_id').val();
    var kim_opisanie = $('#kim_opisanie').val();
    var type_kim= $('#type_kim').val();
    var kim_file = $('#kim').files2('get_file_id');
    var kim_url = $('#kim_url').val();
    var kim_text = $('#kim_text').val();
    var tip_kursa = $('#kurs_type').val();
    var tip = 2;
    if ((type_kim==1 && kim_file=='') || (type_kim==2 && kim_url=='') || (type_kim==3 && kim_text=='')) return false;
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'save_kim',
            theme_id:theme_id,
            kim_opisanie: kim_opisanie,
            type_kim: type_kim,
            kim_file:kim_file,
            kim_url: kim_url,
            kim_text: kim_text,
            tip: tip,
            tip_kursa:tip_kursa
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {
                $(data.html).insertBefore($('#section_footer_kf'+theme_id));
                hide_form('add_kim_form');
            }
            else {
                show_msg('danger', 'КИМ не загружен. Ошибка выполнения запроса к БД.');
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function delete_kim(kim_id,tip){
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_kim',
            kim_id:kim_id,
            tip: tip
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {
                $('#kim'+kim_id).remove();
            }
            else {
                show_msg('danger', 'КИМ не удален. Ошибка выполнения запроса к БД.');
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function edit_kim(kim_id){
    var file_name = $('#kim_file_name'+kim_id).text();
    var file_id = $('#kim_file_id'+kim_id).val();
    var url = $('#kim_url'+kim_id).text();
    var text = $('#kim_text'+kim_id).text();
    var kim_type  = 1;
    if (url) kim_type = 2;
    if (text) kim_type = 3;
    var opisanie = $('#kim_opisanie'+kim_id).text();
    $('#kim_edit_opisanie').val(opisanie);
    $('#type_edit_kim').val(kim_type);
    onchange_kim_type('edit_kim');
    $('#edit_kim_url').val(url);
    $('#edit_kim').files2('set_file',file_id);
    $('#edit_kim_form').removeClass('hidden');
    $('#kim_edit_id').val(kim_id);
    $('#edit_kim_text').val(text);
    var offset = $('#kim'+kim_id+' .data').offset();
    var offset_height = $('#kim'+kim_id+' .data').height();

    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 40;


    $('#edit_kim_form').offset({top:offset.top, left: offset.left});
}

function save_kim_edit(){
    var opisanie = $('#kim_edit_opisanie').val();
    var type_kim = $('#type_edit_kim option:selected').val();
    var file_kim =  $('#edit_kim').files2('get_file_id');
    var kim_url = $('#edit_kim_url').val();
    var kim_id = $('#kim_edit_id').val();
    var kim_text = $('#edit_kim_text').val();
    var tip_kursa = $('#kurs_type').val();
    var tip = $('#kim_tip'+kim_id).val();
    if ((type_kim==1 && file_kim=='') || (type_kim==2 && kim_url=='') || (type_kim==3 && kim_text=='')) return false;
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'save_edit_kim',
            kim_id:kim_id,
            opisanie: opisanie,
            type_kim: type_kim,
            file_kim:file_kim,
            kim_url: kim_url,
            kim_text: kim_text,
            tip_kursa:tip_kursa,
            tip:tip
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {
                $('#kim'+kim_id).replaceWith(data.html);
                //$(data.html).insertBefore($('#section_footer_kf'+theme_id));
                hide_form('edit_kim_form');
            }
            else {
                show_msg('danger', 'КИМ не изменен. Ошибка выполнения запроса к БД.');
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function add_fiak(id){
    $('#fiak_id').val($('#fiak_id option:first').val());
    $('#ia_chasy').val('');
    $('#fiak_opisanie').val('');
    resetms('prepods_fiak');
    $('#fiak_week').val($('#fiak_week option:first').val());
    $('#add_fiak_form').removeClass('hidden');
    $('#attestaciya .data').append($('#add_fiak_form'));
    $('#fiak_id').focus();
}

function is_positive_interger(number){
    if (number > 0 && Math.ceil(number) == number ) return true;
    else return false;
}

function save_fiak(){
    var kurs_id = $('#fiak_kurs_id').val();
    var fiak_id = $('#fiak_id option:selected').val();
    var chasy = $('#ia_chasy').val();
    var opisanie = $('#fiak_opisanie').val();
    var prepods =get_checked('prepods_fiak');
    var week = $('#fiak_week option:selected').val();
    if (chasy && is_positive_interger(chasy)){
        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'save_fiak',
                kurs_id: kurs_id,
                fiak_id : fiak_id,
                chasy : chasy,
                opisanie: opisanie,
                prepods: prepods,
                week: week
            },
            success: function (data) {
                console.log(data);
                if (data.res != 'error') {
                    $(data.html).insertBefore($('#section_footer_attestaciya'));
                    hide_form('add_fiak_form');
                    $('#add_fiak_action').addClass('hidden');
                }
                else {
                    show_msg('danger', data.msg);
                }
            },
            error: function (e, t) {
                console.log(e.responseText);
                show_msg('danger','Ошибка выполнения ajax-запроса!');
            }
        });
    }
}

function delete_fiak(kurs_id){
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_fiak',
            kurs_id: kurs_id
        },
        success: function (data) {
            if (data.res != 'error') {
                $('#fiak'+kurs_id).remove();
                $('#section_footer_fiak'+kurs_id).remove();
                $('#add_fiak_action').removeClass('hidden');
            }
            else {
                show_msg(data.type, data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function edit_fiak(kurs_id){
    var chasy = $('#fiak_chasy'+kurs_id).text();
    var fiak_id = $("#fiak_id"+kurs_id).val();
    var opisanie = $('#fiak_opisanie'+kurs_id).text();
    var week = $('#fiak_week'+kurs_id).text();
    $('#edit_fiak_week').val(week);
    $('#fiak_edit_id').val(fiak_id);
    $('#ia_edit_chasy').val(chasy);
    $('#fiak_edit_opisanie').val(opisanie);
    $('#edit_fiak_form').removeClass('hidden');
    ms_init_data($('#kontrols_ids'+kurs_id).text(),'prepods_fiak_edit');
    var offset = $('#fiak'+kurs_id+' .data').offset();
    var offset_height = $('#fiak'+kurs_id+' .data').height();

    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 30;

    $('#edit_fiak_form').offset({top:offset.top, left: offset.left});
    $('#fiak_edit_id').focus();
}

function save_edit_fiak(){
    var fiak_id = $('#fiak_edit_id').val();
    var chasy = $('#ia_edit_chasy').val();
    var kurs_id = $('#fiak_edit_kurs_id').val();
    var opisanie = $('#fiak_edit_opisanie').val();
    var prepods =get_checked('prepods_fiak_edit');
    var week = $('#edit_fiak_week option:selected').val();
    if (chasy && is_positive_interger(chasy))
        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'edit_fiak',
                kurs_id: kurs_id,
                fiak_id: fiak_id,
                chasy: chasy,
                opisanie: opisanie,
                prepods:prepods,
                week: week
            },
            success: function (data) {
                if (data.res != 'error') {
                    $('#fiak'+kurs_id).replaceWith(data.html);
                    hide_form('edit_fiak_form');
                }
                else {
                    show_msg('danger', data.msg);
                }
            },
            error: function (e, t) {
                console.log(e.responseText);
                show_msg('danger','Ошибка выполнения ajax-запроса!');
            }
        });
}

function add_them_dr(kurs_id){
    $('#theme_dr_name').val('');
    $('#add_theme_dr_form').removeClass('hidden');
    $('#fiak'+kurs_id+' .data').append($('#add_theme_dr_form'));
    $('#theme_dr_name').focus();
}

function save_theme_dr(){
    var kurs_id = $('#theme_dr_kurs_id').val();
    var theme_name = $('#theme_dr_name').val();
    if (theme_name){
        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'save_theme_dr',
                kurs_id: kurs_id,
                theme_name: theme_name
            },
            success: function (data) {
                if (data.res != 'error') {
                    $(data.html).insertBefore($('#section_footer_fiak'+kurs_id));
                    hide_form('add_theme_dr_form');
                }
                else {
                    show_msg('danger', data.msg);
                }
            },
            error: function (e, t) {
                console.log(e.responseText);
                show_msg('danger','Ошибка выполнения ajax-запроса!');
            }
        });
    }
}

function edit_theme_dr(theme_dr_id){
    var theme_dr_name = $('#theme_dr_name'+theme_dr_id).text();
    $('#edit_theme_dr_name').val(theme_dr_name);
    $('#edit_theme_dr_id').val(theme_dr_id);
    $('#theme_dr'+theme_dr_id+' .data').append($('#edit_theme_dr_form'));
    $('#edit_theme_dr_form').removeClass('hidden');
}

function edit_theme_dr_save(){
    var theme_name = $('#edit_theme_dr_name').val();
    var kurs_id = $('#edit_theme_dr_kurs_id').val();
    var theme_dr_id = $('#edit_theme_dr_id').val();
    if (theme_name){
        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'edit_theme_dr',
                kurs_id: kurs_id,
                theme_name: theme_name,
                theme_dr_id: theme_dr_id
            },
            success: function (data) {
                if (data.res != 'error') {
                    $('#theme_dr'+theme_dr_id).replaceWith(data.html);
                    hide_form('edit_theme_dr_form');
                }
                else {
                    show_msg('danger', data.msg);
                }
            },
            error: function (e, t) {
                console.log(e.responseText);
                show_msg('danger','Ошибка выполнения ajax-запроса!');
            }
        });
    }
}

function delete_theme_dr(theme_dr_id){
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_theme_dr',
            theme_dr_id: theme_dr_id
        },
        success: function (data) {
            if (data.res != 'error') {
                $('#theme_dr'+theme_dr_id).remove();
            }
            else {
                show_msg('danger', data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function add_podrazdel_fk(podrazdel_id){
    $('#forma_kontrolya_podrazdel').val($('#forma_kontrolya_podrazdel option:first').val());
    $('#fk_podrazdel_chasy').val('');
    $('#podrazdel_kf_id').val(podrazdel_id);
    resetms('prepods_podrazdel_kf');
    var offset = $('#podrazdel'+podrazdel_id+' .data').offset();
    var offset_height = $('#podrazdel'+podrazdel_id+' .data').height();
    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 20;
    $('#add_podrazdel_kf_form').removeClass('hidden');
    $('#add_podrazdel_kf_form').offset({top:offset.top, left: offset.left});
}

function save_podrazdel_kf(){
    var podrazdel_id = $('#podrazdel_kf_id').val();
    var chasy = $('#fk_podrazdel_chasy').val();
    var kf_id = $('#forma_kontrolya_podrazdel option:selected').val();
    var prepods = get_checked('prepods_podrazdel_kf');
    if (is_positive_interger(chasy) && isEven(chasy)) {
        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'add_podrazdel_kf',
                podrazdel_id: podrazdel_id,
                chasy: chasy,
                kf_id: kf_id,
                prepods:prepods
            },
            success: function (data) {
                if (data.res != 'error') {
                    $(data.html).insertBefore($('#section_footer_podrazdel_kf'+podrazdel_id));
                    $('#add_podrazdel_kf_action'+podrazdel_id).addClass('hidden');
                    hide_form('add_podrazdel_kf_form');
                }
                else {
                    show_msg('danger', data.msg);
                }
            },
            error: function (e, t) {
                console.log(e.responseText);
                show_msg('danger', 'Ошибка выполнения ajax-запроса!');
            }
        });
    }
    else{
        show_msg('warning','Количество часов должно быть положительным целым числом кратное 2');
    }
}

function edit_podrazdel_kf(podrazdel_id){
    $('#edit_podrazdel_kf_id').val(podrazdel_id);
    var kf_id = $('#podrazdel_kf_id'+podrazdel_id).val();
    var chasy = $('#chasy_kf_podrazdel'+podrazdel_id).text();
    $('#edit_forma_kontrolya_podrazdel').val(kf_id);
    $('#edit_fk_podrazdel_chasy').val(chasy);
    ms_init_data($('#kontrols_pk_ids'+podrazdel_id).text(),'prepods_podrazdel_kf_edit');
    var offset = $('#podrazdel_kf'+podrazdel_id+' .data').offset();
    var offset_height = $('#podrazdel_kf'+podrazdel_id+' .data').height();
    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 20;
    $('#edit_podrazdel_kf_form').removeClass('hidden');
    $('#edit_podrazdel_kf_form').offset({top:offset.top, left: offset.left});
    $('#edit_forma_kontrolya_podrazdel').focus();
}

function save_edit_podrazdel_kf(){
    var podrazdel_id = $('#edit_podrazdel_kf_id').val();
    var kf_id = $('#edit_forma_kontrolya_podrazdel').val();
    var chasy = $('#edit_fk_podrazdel_chasy').val();
    var prepods = get_checked('prepods_podrazdel_kf_edit');
    //console.log(prepods);
    if (is_positive_interger(chasy) && isEven(chasy)) {
        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'edit_podrazdel_kf',
                podrazdel_id: podrazdel_id,
                chasy: chasy,
                kf_id: kf_id,
                prepods:prepods
            },
            success: function (data) {
                if (data.res != 'error') {
                    $('#podrazdel_kf'+podrazdel_id).replaceWith(data.html);
                    hide_form('edit_podrazdel_kf_form');
                }
                else {
                    show_msg('danger', data.msg);
                }
            },
            error: function (e, t) {
                console.log(e.responseText);
                show_msg('danger', 'Ошибка выполнения ajax-запроса!');
            }
        });
    }
    else{
        show_msg('warning','Количество часов должно быть положительным целым числом кратное 2');
    }
}

function delete_podrazdel_kf(podrazdel_id){
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_podrazdel_kf',
            podrazdel_id: podrazdel_id
        },
        success: function (data) {
            if (data.res != 'error') {
                $('#podrazdel_kf'+podrazdel_id).remove();
                $('#add_podrazdel_kf_action'+podrazdel_id).removeClass('hidden');
            }
            else {
                show_msg('danger', data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger', 'Ошибка выполнения ajax-запроса!');
        }
    });
}

function add_podrazdel_kf_kim(podrazdel_id){
    $('#podrazdel_kf_kim_opisanie').val('');
    $('#type_podrazdel_kim').val($('#type_podrazdel_kim option:first').val());
    $('#podrazdel_kim').files2('set_file',-1);
    $('#podrazdel_kim_url').val('');
    $('#podrazdel_kim_text').val('');
    $('#podrazdel_kim_id').val(podrazdel_id);
    var offset = $('#podrazdel_kf'+podrazdel_id+' .data').offset();
    var offset_height = $('#podrazdel_kf'+podrazdel_id+' .data').height();
    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 20;
    $('#add_podrazdel_kim_form').removeClass('hidden');
    $('#add_podrazdel_kim_form').offset({top:offset.top, left: offset.left});
    $('#podrazdel_kf_kim_opisanie').focus();
}

function save_podrazdel_kim(){
    var podrazdel_id =$('#podrazdel_kim_id').val();
    var kim_opisanie = $('#podrazdel_kf_kim_opisanie').val();
    var type_kim= $('#type_podrazdel_kim').val();
    var kim_file = $('#podrazdel_kim').files2('get_file_id');
    var kim_url = $('#podrazdel_kim_url').val();
    var kim_text = $('#podrazdel_kim_text').val();
    var tip_kursa = $('#kurs_type').val();
    var tip = 1;
    if ((type_kim==1 && kim_file=='') || (type_kim==2 && kim_url=='') || (type_kim==3 && kim_text=='')) return false;
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'save_podrazdel_kim',
            podrazdel_id:podrazdel_id,
            kim_opisanie: kim_opisanie,
            type_kim: type_kim,
            kim_file:kim_file,
            kim_url: kim_url,
            kim_text: kim_text,
            tip_kursa: tip_kursa,
            tip:tip
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {
                $(data.html).insertBefore($('#section_footer_kf_podrazdela'+podrazdel_id));
                hide_form('add_podrazdel_kim_form');
            }
            else {
                show_msg('danger', 'КИМ не загружен. Ошибка выполнения запроса к БД.');
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}

function add_podrazdel_umk(podrazdel_id){
    $('#add_podrazdel_umk_form').removeClass('hidden');
    var offset = $('#podrazdel'+podrazdel_id+' .data').offset();
    var offset_height = $('#podrazdel'+podrazdel_id+' .data').height();

    offset.top = offset.top + offset_height+10;
    offset.left = offset.left + 30;

    $('#add_podrazdel_umk_form').offset({top:offset.top, left: offset.left});


    $('#podrazdel_umk_opisanie').val('');
    $('#umk_podrazdel_id').val(podrazdel_id);
    $("#podrazdel_umk").files2('set_file',-1);
    $('#type_podrazdel_umk').val($('#type_podrazdel_umk option:first').val());
    change_umk_type('podrazdel_umk');
    $('#umk_urlpodrazdel_umk').val('');
    //$('#umk_url_blockumk').addClass('hidden');
    //$('#umk_file_blockumk').removeClass('hidden');
    $('#podrazdel_umk_opisanie').focus();
}

function save_podrazdel_umk(){
    var opisanie = $("#podrazdel_umk_opisanie").val();
    var umk_type = $('#type_podrazdel_umk option:selected').val();
    var url = $('#umk_urlpodrazdel_umk').val();
    var file = $('#podrazdel_umk').files2('get_file_id');
    var podrazdel_id = $('#umk_podrazdel_id').val();
    var tip_kursa = $('#kurs_type').val();
    var tip = 1;
    if ((umk_type==1 && file=='') || (umk_type==2 && url=='')) return false;
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'add_podrazdel_umk',
            podrazdel_id: podrazdel_id,
            umk_type: umk_type,
            file: file,
            url: url,
            opisanie:opisanie,
            tip_kursa:tip_kursa,
            tip:tip
        },
        success: function (data) {
            console.log(data);
            if (data.res == 'nothing') return false;
            if (data.res != 'error') {
                hide_form('add_podrazdel_umk_form');
                $(data.html).insertBefore($('#section_footer_podrazdel_kf'+podrazdel_id));
            }
            else {
                show_msg(data.type, data.msg);
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}




var podrazdel_numbers = {};

function swap_podrazdels(first,second){
    var cur_tr = first;
    var first_html = '';
    var second_html = '';
    while (true){
        first_html+=$(cur_tr)[0].outerHTML;
        var next_tr = cur_tr.next('tr');
        if (!$(cur_tr).hasClass('podrazdel-row')) $(cur_tr).remove();
        if (cur_tr.hasClass('section_footer_podrazdel')) break;
        cur_tr = next_tr;
    }
    cur_tr = second;
    while (true){
        second_html+=$(cur_tr)[0].outerHTML;
        var next_tr = cur_tr.next('tr');
        if (!$(cur_tr).hasClass('podrazdel-row')) $(cur_tr).remove();
        if (cur_tr.hasClass('section_footer_podrazdel')) break;
        cur_tr = next_tr;
    }
    $(first).replaceWith(second_html);
    $(second).replaceWith(first_html);
}

function get_num_of_podrazdel(podrazdel){
    var number = $(podrazdel).find('.podrazdel_nomer');
    return parseInt($(number).val());
}

function get_podrazdel_id(podrazdel){
    var podrazdel_id = $(podrazdel).find('.podrazdel_id');
    return parseInt($(podrazdel_id).val());
}

function set_num_podrazdel(podrazdel,value){
    var number = $(podrazdel).find('.podrazdel_nomer');
    $(number).val(value);
}

var podrazdelTimerId = {};

function savePodrazdelNumbers() {
    //console.log(themes_numbers);
    //return 0 ;
    for (var i in podrazdel_numbers){
        if (podrazdel_numbers.hasOwnProperty(i)){
            if (podrazdel_numbers[i].old == podrazdel_numbers[i].new) delete podrazdel_numbers[i];
        }
    }
    if (!$.isEmptyObject(podrazdel_numbers)) {

        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'save_podrazdel_num_order',
                order: podrazdel_numbers
            },
            success: function (data) {
                console.log(data);
                if (data.res == 'nothing') return false;
                if (data.res != 'error') {

                }
                else {
                    show_msg(data.type, data.msg);
                }
            },
            error: function (e, t) {
                console.log(e.responseText);
                show_msg('danger', 'Ошибка выполнения ajax-запроса!');
            }
        });
    }
    //console.log(themes_numbers);
}

function onPodrazdelTimer() {
    podrazdelTimerId = setTimeout('savePodrazdelNumbers()', 3000);
}

function offPodrazdelTimer() {
    clearTimeout(podrazdelTimerId)
}

function podrazdel_up(podrazdel_id,razdel_id){
    offPodrazdelTimer();
    onPodrazdelTimer();
    var prev_tr = $('#podrazdel'+podrazdel_id).prevAll('.podrazdel'+razdel_id).first();
    var podrazdel = $('#podrazdel'+podrazdel_id);
    if (prev_tr.length>0) {
        var cur_num = get_num_of_podrazdel(podrazdel);
        var prev_podrazdel_id = get_podrazdel_id(prev_tr);
        if (!podrazdel_numbers.hasOwnProperty(podrazdel_id)){
            podrazdel_numbers[podrazdel_id] = {old: cur_num , new: cur_num-1};
        }
        else{
            podrazdel_numbers[podrazdel_id].new = cur_num-1;
        }
        set_num_podrazdel(podrazdel,cur_num-1);
        if (!podrazdel_numbers.hasOwnProperty(prev_podrazdel_id)){
            podrazdel_numbers[prev_podrazdel_id] = {old: cur_num-1 , new: cur_num};
        }
        else{
            podrazdel_numbers[prev_podrazdel_id].new = cur_num;
        }
        set_num_podrazdel(prev_tr,cur_num);
        swap_podrazdels($(podrazdel), $(prev_tr));
        recalculculate_order_num();
    }
}

function podrazdel_down(podrazdel_id,razdel_id){
    offPodrazdelTimer();
    onPodrazdelTimer();
    var next_tr = $('#podrazdel'+podrazdel_id).nextAll('.podrazdel'+razdel_id).first();
    var podrazdel = $('#podrazdel'+podrazdel_id);
    if (next_tr.length>0) {
        var cur_num = get_num_of_podrazdel(podrazdel);
        var next_podrazdel_id = get_podrazdel_id(next_tr);
        if (!podrazdel_numbers.hasOwnProperty(podrazdel_id)){
            podrazdel_numbers[podrazdel_id] = {old: cur_num , new: cur_num+1};
        }
        else{
            podrazdel_numbers[podrazdel_id].new = cur_num+1;
        }
        set_num_podrazdel(podrazdel,cur_num+1);
        if (!podrazdel_numbers.hasOwnProperty(next_podrazdel_id)){
            podrazdel_numbers[next_podrazdel_id] = {old: cur_num+1 , new: cur_num};
        }
        else{
            podrazdel_numbers[next_podrazdel_id].new = cur_num;
        }
        set_num_podrazdel(next_tr,cur_num);
        swap_podrazdels($(podrazdel), $(next_tr));
        recalculculate_order_num();
    }
}

//<editor-fold desc="Перемещение темы">
var themes_numbers = {};

function swap_theme(first,second){
    var cur_tr = first;
    var first_html = '';
    var second_html = '';
    while (true){
        first_html+=$(cur_tr)[0].outerHTML;
        var next_tr = cur_tr.next('tr');
        if (!$(cur_tr).hasClass('theme-row')) $(cur_tr).remove();
        if (cur_tr.hasClass('section_footer_theme')) break;
        cur_tr = next_tr;
    }
    cur_tr = second;
    while (true){
        second_html+=$(cur_tr)[0].outerHTML;
        var next_tr = cur_tr.next('tr');
        if (!$(cur_tr).hasClass('theme-row')) $(cur_tr).remove();
        if (cur_tr.hasClass('section_footer_theme')) break;
        cur_tr = next_tr;
    }
    $(first).replaceWith(second_html);
    $(second).replaceWith(first_html);
}

function get_num_of_themes(theme){
    var number = $(theme).find('.theme_nomers');
    return parseInt($(number).val());
}

function get_theme_id(theme){
    var theme_id = $(theme).find('.theme_id');
    return parseInt($(theme_id).val());
}

function set_num_theme(theme,value){
    var number = $(theme).find('.theme_nomers');
    $(number).val(value);
}

var tId = {};

function goTimer() {
    //console.log(themes_numbers);
    //return 0 ;
    for (var i in themes_numbers){
        if (themes_numbers.hasOwnProperty(i)){
            if (themes_numbers[i].old == themes_numbers[i].new) delete themes_numbers[i];
        }
    }
    if (!$.isEmptyObject(themes_numbers)) {

        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'save_theme_num_order',
                order: themes_numbers
            },
            success: function (data) {
                console.log(data);
                if (data.res == 'nothing') return false;
                if (data.res != 'error') {

                }
                else {
                    show_msg(data.type, data.msg);
                }
            },
            error: function (e, t) {
                console.log(e.responseText);
                show_msg('danger', 'Ошибка выполнения ajax-запроса!');
            }
        });
    }
    //console.log(themes_numbers);
}

function onTimer() {
    tId = setTimeout('goTimer()', 3000);
}

function offTimer() {
   clearTimeout(tId)
}

function theme_up(theme_id,podrazdel_id){
 offTimer();
 onTimer();
 var prev_tr = $('#theme'+theme_id).prevAll('.theme'+podrazdel_id).first();
 var theme = $('#theme'+theme_id);
 if (prev_tr.length>0) {
     var cur_num = get_num_of_themes(theme);
     var prev_theme_id = get_theme_id(prev_tr);
     if (!themes_numbers.hasOwnProperty(theme_id)){
         themes_numbers[theme_id] = {old: cur_num , new: cur_num-1};
     }
     else{
         themes_numbers[theme_id].new = cur_num-1;
     }
     set_num_theme(theme,cur_num-1);
     if (!themes_numbers.hasOwnProperty(prev_theme_id)){
         themes_numbers[prev_theme_id] = {old: cur_num-1 , new: cur_num};
     }
     else{
         themes_numbers[prev_theme_id].new = cur_num;
     }
     set_num_theme(prev_tr,cur_num);
     swap_theme($(theme), $(prev_tr));
     recalculculate_order_num();
 }
}

function theme_down(theme_id,podrazdel_id){
    offTimer();
    onTimer();
    var next_tr = $('#theme'+theme_id).nextAll('.theme'+podrazdel_id).first();
    var theme = $('#theme'+theme_id);
    if (next_tr.length>0) {
        var cur_num = get_num_of_themes(theme);
        var next_theme_id = get_theme_id(next_tr);
        if (!themes_numbers.hasOwnProperty(theme_id)){
            themes_numbers[theme_id] = {old: cur_num , new: cur_num+1};
        }
        else{
            themes_numbers[theme_id].new = cur_num+1;
        }
        set_num_theme(theme,cur_num+1);
        if (!themes_numbers.hasOwnProperty(next_theme_id)){
            themes_numbers[next_theme_id] = {old: cur_num+1 , new: cur_num};
        }
        else{
            themes_numbers[next_theme_id].new = cur_num;
        }
        set_num_theme(next_tr,cur_num);
        swap_theme($(theme), $(next_tr));
        recalculculate_order_num();
    }
}
//</editor-fold>

function change_podpis(kurs_id){
    var is_checked = $('#status_programmy').prop('checked');
        $.ajax({
            url: "/kurs/ajax",
            type: "POST",
            dataType: "json",
            data: {
                ajax_query: 'check_kurs',
                kurs_id:kurs_id,
                is_checked:is_checked ? 1 :0
            },
            success: function (data) {
                console.log(data);
                if (data.res != 'error') {

                }
                else {
                    $('#status_programmy').prop('checked',!is_checked);
                    show_msg('danger', data.msg,7000);
                }
            },
            error: function (e, t) {
                $('#status_programmy').prop('checked',false);
                console.log(e.responseText);
                show_msg('danger', 'Ошибка выполнения ajax-запроса!');
            }
        });
}

function add_kurs_kim(kurs_id){
    $('#kim_kurs_id').val(kurs_id);
    $('#kurs_kim_opisanie').val('');
    $('#type_kurs_kim').val(1);
    onchange_kim_type('kurs_kim');
    $('#kurs_kim').files2('set_file',-1);
    $('#kurs_kim_url').val('');
    $('#kurs_kim_text').val('');

    $('#add_kurs_kim_form').removeClass('hidden');
    var offset = $('#fiak'+kurs_id+' .data').offset();
    var offset_height = $('#fiak'+kurs_id+' .data').height();

    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 10;

    $('#add_kurs_kim_form').offset({top:offset.top, left: offset.left});
}

function save_kurs_kim(){
    var kurs_id =$('#kim_kurs_id').val();
    var kim_opisanie = $('#kurs_kim_opisanie').val();
    var type_kim= $('#type_kurs_kim').val();
    var kim_file = $('#kurs_kim').files2('get_file_id');
    var kim_url = $('#kurs_kim_url').val();
    var kim_text = $('#kurs_kim_text').val();
    var tip_kursa = $('#kurs_type').val();
    var tip = 3;
    if ((type_kim==1 && kim_file=='') || (type_kim==2 && kim_url=='') || (type_kim==3 && kim_text=='')) return false;
    $.ajax({
        url: "/kurs/ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'save_kurs_kim',
            kurs_id:kurs_id,
            kim_opisanie: kim_opisanie,
            type_kim: type_kim,
            kim_file:kim_file,
            kim_url: kim_url,
            kim_text: kim_text,
            tip_kursa:tip_kursa,
            tip:tip
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {
                $(data.html).insertBefore($('#section_footer_fiak'+kurs_id));
                hide_form('add_kurs_kim_form');
            }
            else {
                show_msg('danger', 'КИМ не загружен. Ошибка выполнения запроса к БД.');
            }
        },
        error: function (e, t) {
            console.log(e.responseText);
            show_msg('danger','Ошибка выполнения ajax-запроса!');
        }
    });
}
