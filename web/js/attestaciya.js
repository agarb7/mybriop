
function onChangeDolzhnost(object){
    if ($(object).val() == -1) {

        var url = '/attestaciya/add-dolzhnost/';
        var fizLicoId = $(object).data("fizlicoid");
        var modalContainer = $('#dolzhnostModal');
        var modalBody = modalContainer.find('.modal-body');
        briop_ajax({
            url: url,
            data: {'fizLicoId': fizLicoId},
            done: function(answer){
                $('#modal_content').html(answer);
                $('#myModal').fadeIn(500);
                $('#myModal').focus();
                //$(modalBody).html(answer);
                //$(modalContainer).modal('show');
            }
        });
    }
}

function showOrganizaciyaNazvanie(organizaciyaInputId){
    $('#'+organizaciyaInputId).toggleClass('hidden');
}

function onOrganizaciyaNazvanieKeyUp(organizaciyaIdInput,value,event){
    if (value != '')
        $('#'+organizaciyaIdInput).select2('val','');
}

function onOrganizaciyaIdChange(organizaciyaIdInput, organizaciyaNazvanieInput){
    if ($('#'+organizaciyaIdInput).val()){
        $('#'+organizaciyaNazvanieInput).val('');
    }
}

function onChangeKategoriya(kategoriyaInput){
    var cur_value = $('#'+kategoriyaInput+' option:selected').val();
    switch (cur_value) {
        case 'pervaya_kategoriya':
            $('#varIspytanie2Div').addClass('hidden');
            $('#varIspytanie3Div').addClass('hidden');
            $('#panel-o-sebe').addClass('hidden');
            break;
        case 'vyshaya_kategoriya':
            $('#varIspytanie2Div').removeClass('hidden');
            $('#varIspytanie3Div').removeClass('hidden')
            $('#panel-o-sebe').removeClass('hidden');
            break;
    }
}

function addVisheeObrazovanie(){
    var visheeObrazovanieCounter = $('#visheeObrazovanieCounter').val();
    briop_ajax({
        url: '/attestaciya/add-vishee-obrazovanie/',
        data: {
          num: visheeObrazovanieCounter
        },
        done: function (answer){
            $('#vissheeObrazovanieCntr').append(answer);

            var scroll_el = $('#panel'+visheeObrazovanieCounter);

            $('html, body').animate({ scrollTop: $(scroll_el).offset().top }, 500);

            visheeObrazovanieCounter++;
            $('#visheeObrazovanieCounter').val(visheeObrazovanieCounter);
        }
    });
}

function deletVO(obrazovanieDlyaZayavleniyaId,object){
    var panel = $(object).closest('.panel');
    if (obrazovanieDlyaZayavleniyaId){
        panel.addClass('hidden');
        panel.find('.udalit_input').val(1);
    }
    else{
        panel.remove();
    }
}

function addKurs(){
    var kursyCounter = $('#kursyCounter').val();
    briop_ajax({
        url: '/attestaciya/add-kurs/',
        data: {
            num: kursyCounter
        },
        done: function (answer){
            $('#KursyCntr').append(answer);

            var scroll_el = $('#panel'+kursyCounter);

            $('html, body').animate({ scrollTop: $(scroll_el).offset().top }, 500);

            kursyCounter++;
            $('#kursyCounter').val(kursyCounter);
        }
    });
}

function deletKurs(obrazovanieDlyaZayavleniyaId,object){
    var panel = $(object).closest('.panel');
    if (obrazovanieDlyaZayavleniyaId){
        panel.addClass('hidden');
        panel.find('.udalit_input').val(1);
    }
    else{
        panel.remove();
    }
}


$(function(){

    $(document).on('submit','#dolzhnostForm',function (e){
        e.preventDefault();
        var form = $(this);
        if ($(form).find('has-error').length>0){
            return false;
        }
        briop_ajax({
            url: '/attestaciya/submit-add-dolzhnost/',
            data: form.serialize(),
            done: function (answer){
                if (answer.result == true){
                    $('#registraciya-dolzhnost :last').before(
                        $('<option></option>')
                            .val(answer.data.rabota_fiz_lica_id)
                            .text(answer.data.dolhnost)
                    );
                    $('#registraciya-dolzhnost').val(answer.data.rabota_fiz_lica_id);
                    $('#registraciya-dolzhnost').change();
                    close_modal();
                    //$('#dolzhnostModal').modal('hide');
                    bsalert('Должность успешно добавлена');
                }
                else{
                    //var modalContainer = $('#dolzhnostModal');
                    //var modalBody = modalContainer.find('.modal-body');
                    //$(modalBody).html(answer);
                    $('#modal_content').html(answer);
                }
            }
        });
    });

    $('#registraciya-kategoriya').change();

    $('#attestacionnyListKategoriya').change();

});

function close_modal(){
    $('#myModal').fadeOut(500);
}

function modalKeyDown(event){
    if (event.keyCode === 27){
        close_modal();
    }
}

function onChangeCurrentCategoriya(){
    var selected = $('#attestacionnyListKategoriya option:selected').val();
    if (selected == 'bez_kategorii'){
        $('#preiod_dejstviya').hide();
        $('#copiya_lista').hide();
    }
    else{
        $('#preiod_dejstviya').show();
        $('#copiya_lista').show();
    }
}


