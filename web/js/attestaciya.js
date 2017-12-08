
function onChangeDolzhnost(object){
    if ($(object).val() == -1) {

        var url = '/attestaciya/add-dolzhnost/';
        var fizLicoId = $(object).data("fizlicoid");
        //var modalContainer = $('#dolzhnostModal');
        //var modalBody = modalContainer.find('.modal-body');
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
    else{
        //var dolznostId = dolzhnosti[$('#registraciya-dolzhnost').val()];
        var dolzhnostId = +$('#registraciya-dolzhnost').val().split('_')[1];
        var rajonId = +$('#registraciya-dolzhnost').val().split('_')[2];
        var uchdolzhnosti = $(object).data("uchdolzhnosti");
        var buryatia = $(object).data("buryatia");
        if (dolzhnostId == 47){
            $('#registraciya-stazh_obshij_trudovoj').parent().parent().removeClass('hidden');
            $('#registraciya-stazh_rukovodyashej_raboty').parent().parent().removeClass('hidden');
            $('#registraciya-pedstazhvdolzhnosti').parent().parent().addClass('hidden');
        } else {
            $('#registraciya-stazh_obshij_trudovoj').parent().parent().addClass('hidden');
            $('#registraciya-stazh_rukovodyashej_raboty').parent().parent().addClass('hidden');
            $('#registraciya-pedstazhvdolzhnosti').parent().parent().removeClass('hidden');
        }
        if ($.inArray(dolzhnostId, uchdolzhnosti) == -1){
            $('.field-registraciya-isfgos').hide();
        } else {
            $('.field-registraciya-isfgos').show();
        }
        if ($.inArray(rajonId, buryatia) == -1 && rajonId != NaN){
            $('.field-registraciya-rabotarajonid').show();
            $('#rajonModal').modal('show');
        } else {
            $('.field-registraciya-rabotarajonid').hide();
            $('#rajonId').val(rajonId);
        }
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
    //var dolznostId = dolzhnosti[$('#registraciya-dolzhnost').val()];
    var dolzhnostId = +$('#registraciya-dolzhnost').val().split('_')[1];
    switch (cur_value) {
        case 'pervaya_kategoriya':
            $('#varIspytanie2Div').addClass('hidden');
            $('#varIspytanie3Div').addClass('hidden');
            $('#panel-o-sebe').addClass('hidden');
            $('#panel-otraslevoe-soglashenie').addClass('hidden');
            $('#prilozheni1').removeClass('hidden');
            $('#ld').addClass('hidden');
            break;
        case 'vyshaya_kategoriya':
            $('#varIspytanie2Div').removeClass('hidden');
            if ($('#otraslevoeSoglashenieCntr .panel').length == 0 && dolzhnostId != 47) {
                $('#varIspytanie3Div').removeClass('hidden');
            }
            else{
                $('#varIspytanie3Div').addClass('hidden');
            }
            $('#panel-o-sebe').addClass('hidden');
            if (dolzhnostId != 47) {
                $('#panel-otraslevoe-soglashenie').removeClass('hidden');
            }
            else{
                $('#panel-otraslevoe-soglashenie').addClass('hidden');
            }
            $('#prilozheni1').addClass('hidden');
            $('#ld').removeClass('hidden');
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
    if (confirm('Вы дествительно хотите удалить образование?')) {
        var panel = $(object).closest('.panel');
        if (obrazovanieDlyaZayavleniyaId) {
            panel.addClass('hidden');
            panel.find('.udalit_input').val(1);
        }
        else {
            panel.remove();
        }
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
    if (confirm('Вы дествительно хотите удалить курс?')) {
        var panel = $(object).closest('.panel');
        if (obrazovanieDlyaZayavleniyaId) {
            panel.addClass('hidden');
            panel.find('.udalit_input').val(1);
        }
        else {
            panel.remove();
        }
    }
}

function addOtraslevoeSoglashenie(){
    var otraslevoeSoglashenieCounter = $('#otraslevoeSoglashenieCounter').val();
    briop_ajax({
        url: '/attestaciya/add-otraslevoe-soglashenie/',
        data: {
            num: otraslevoeSoglashenieCounter
        },
        done: function (answer){
            //console.log(answer);
            var varIspytabie3Select = $('#varIspytanie3Div select');
            if (!varIspytabie3Select.prop('disabled')){
                varIspytabie3Select.prop('disabled', true);
                $('#varIspytanie3Div').addClass('hidden');
            }

            $('#otraslevoeSoglashenieCntr').append(answer);

            //var scroll_el = $('#panelos'+otraslevoeSoglashenieCounter);

            //$('html, body').animate({ scrollTop: $(scroll_el).offset().top-50 }, 500);

            otraslevoeSoglashenieCounter++;
            $('#otraslevoeSoglashenieCounter').val(otraslevoeSoglashenieCounter);
        }
    });
}

function deleteOtraslevoeSoglashenie(id,object){
    if (confirm('Вы дествительно хотите удалить достижение?')) {
        var panel = $(object).closest('.panel');
        if (id) {
            panel.addClass('hidden');
            panel.find('.udalit_input').val(1);
        }
        else {
            panel.remove();
        }
        if ($('#otraslevoeSoglashenieCntr .panel').length == 0) {
            var varIspytabie3Select = $('#varIspytanie3Div select');
            if (varIspytabie3Select.prop('disabled')) {
                varIspytabie3Select.prop('disabled', false);
                $('#varIspytanie3Div').removeClass('hidden');
            }
        }
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
                    dolzhnosti[answer.data.rabota_fiz_lica_id] = answer.data.dolhnostId;
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

    $('#registraciya-dolzhnost').change();

    $('#changeStatusBtn').click(function(){
       if (confirm('Вы уверены, то хотите передать заявление в отдел аттестации?')){
           briop_ajax({
               url: '/attestaciya/move-to-oa',
               data: {
                   id: $('#registraciya-id').val()
               },
               done: function(response){
                    if (response.type != 'error'){
                        $('#changeStatusBtn').remove();
                        $('#smbBtn').remove();
                        bsalert(response.msg, 'success');
                    }
                    else{
                        bsalert(response.msg, 'danger');
                    }
               }
           });
       }
    });

});

function close_modal(){
    $('#myModal').fadeOut(500);
    $('#registraciya-dolzhnost').val($('#registraciya-dolzhnost option:first').val());
    $('#registraciya-dolzhnost').blur();
    $('#rajonModal').modal('toggle');
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
        $('#data_okonchaniya_attestacii').hide();
    }
    else{
        $('#preiod_dejstviya').show();
        $('#copiya_lista').show();
        $('#data_okonchaniya_attestacii').show();
    }
}

function onPodtverditObrabotku(){
    var checked = $('#podtvershdenieNaObrabotku').prop('checked');
    if (checked){
        $('#smbBtn').prop('disabled',false);
    }
    else{
        $('#smbBtn').prop('disabled',true);
    }
}


