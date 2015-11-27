$(document).ready(function(){

    $('#rpd_smb_btn').click(function(){
        //$('button[type=submit]').remo
        //$(this).attr('clicked','true');
        $('#podrazdel-form').submit();
    });

    recalculculate_order_num();
});


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

function recalculculate_order_num(){
    var razdel_num = $('#razdel_nomer').val();
    var podrazdel_num = $('#podrazdel_nomer').val();
    var theme_num = 0;
    $('.numbered').each(function(){
        if ($(this).hasClass('podrazdel-row')) {
            theme_num=0;
            $(this).find('.num').text(razdel_num+'.'+podrazdel_num);
        }
        if ($(this).hasClass('theme-row')) {
            theme_num++;
            $(this).find('.num').text(razdel_num+'.'+podrazdel_num+'.'+theme_num);
        }
    });
}

function change_podpis(id){
    var is_checked = $('#status_discipliny').prop('checked');
    $.ajax({
        url: "/kurs/rpd-ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'sign_discipline',
            id:id,
            is_checked:is_checked ? 1 :0
        },
        success: function (data) {
            console.log(data);
            if (data.res != 'error') {

            }
            else {
                $('#status_discipliny').prop('checked',!is_checked);
                show_msg('danger', data.msg,7000);
            }
        },
        error: function (e, t) {
            $('#status_discipliny').prop('checked',false);
            console.log(e.responseText);
            show_msg('danger', 'Ошибка выполнения ajax-запроса!');
        }
    });
}

function isEven (someNumber) {
    return (someNumber % 2 == 0) ? true : false;
};

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
                url: "/kurs/rpd-ajax",
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
                url: "/kurs/rpd-ajax",
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
        url: "/kurs/rpd-ajax",
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
    $.ajax({
        url: "/kurs/rpd-ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'save_kf',
            theme_id:theme_id,
            forma_kf_id:forma_kf_id
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
    var kf_id = $('#forma_kontrolya_temi_edit option:selected').val();
    $.ajax({
        url: "/kurs/rpd-ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'save_edit_kf',
            theme_id:theme_id,
            kf_id:kf_id
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
        url: "/kurs/rpd-ajax",
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
    var tip = 2;
    if ((type_kim==1 && kim_file=='') || (type_kim==2 && kim_url=='') || (type_kim==3 && kim_text=='')) return false;
    $.ajax({
        url: "/kurs/rpd-ajax",
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
            tip:tip
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

function delete_kim(kim_id){
    var tip = $('#kim_tip'+kim_id).val();
    $.ajax({
        url: "/kurs/rpd-ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_kim',
            kim_id:kim_id,
            tip:tip
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
    var file_kim = $('#edit_kim').files2('get_file_id');
    var kim_url = $('#edit_kim_url').val();
    var kim_id = $('#kim_edit_id').val();
    var kim_text = $('#edit_kim_text').val();
    var tip = $('#kim_tip'+kim_id).val();
    var tip_kursa = $('#kurs_type').val();
    if ((type_kim==1 && file_kim=='') || (type_kim==2 && kim_url=='') || (type_kim==3 && kim_text=='')) return false;
    $.ajax({
        url: "/kurs/rpd-ajax",
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
            tip:tip,
            tip_kursa: tip_kursa
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
        url: "/kurs/rpd-ajax",
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
    var tip=1;
    if ((umk_type==1 && file=='') || (umk_type==2 && url=='')) return false;
    $.ajax({
        url: "/kurs/rpd-ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'add_podrazdel_umk',
            podrazdel_id: podrazdel_id,
            umk_type: umk_type,
            file: file,
            url: url,
            opisanie:opisanie,
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

function edit_umk(umk_id){
    $('#edit_umk_form').removeClass('hidden');
    var offset = $('#umk'+umk_id+' .data').offset();
    var offset_height = $('#umk'+umk_id+' .data').height();

    offset.top = offset.top + offset_height+20;
    offset.left = offset.left + 30;


    $('#edit_umk_form').offset({top:offset.top, left: offset.left});

    var opisanie = $('#umk_opisanie'+umk_id).text();
    var file_id = $('#umk_file_id'+umk_id).val();
    var url = $('#url'+umk_id).text();
    var ftype = 1;
    if (url!='') ftype=2;

    $('#type_umk_edit').val(ftype);
    $('#umk_edit_opisanie').val(opisanie);
    change_umk_type('umk_edit');
    $('#umk_edit').files2('set_file',file_id);
    $('#umk_urlumk_edit').val(url);
    if (ftype == 2){
        $('#btnumk_filesumk_edit').text('Выберите файл');
        $('#umk_filesumk_edit').val('');
    }
    $('#umk_edit_opisanie').focus();
    $('#umk_id').val(umk_id);
}


function save_edit_umk(){
    var opisanie = $("#umk_edit_opisanie").val();
    var umk_type = $('#type_umk_edit option:selected').val();
    var url = $('#umk_urlumk_edit').val();
    var file = $('#umk_edit').files2('get_file_id');
    var umk_id = $('#umk_id').val();
    var tip = $('#tip'+umk_id).val();
    if ((umk_type==1 && file=='') || (umk_type==2 && url=='')) return false;
    $.ajax({
        url: "/kurs/rpd-ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'edit_umk',
            umk_id: umk_id,
            umk_type: umk_type,
            file: file,
            url: url,
            opisanie:opisanie,
            tip:tip
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
        url: "/kurs/rpd-ajax",
        type: "POST",
        dataType: "json",
        data: {
            ajax_query: 'delete_umk',
            umk_id: umk_id,
            tip:tip
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
    console.log(themes_numbers);
    //return 0 ;
    for (var i in themes_numbers){
        if (themes_numbers.hasOwnProperty(i)){
            if (themes_numbers[i].old == themes_numbers[i].new) delete themes_numbers[i];
        }
    }
    if (!$.isEmptyObject(themes_numbers)) {

        $.ajax({
            url: "/kurs/rpd-ajax",
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