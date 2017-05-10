function getZayavlenieId(element_id){
    return $("#"+element_id).data('zayavlenieId');
}

function getFileId(element_id){
    return $('#'+element_id).find('input').val();
}

function select_portfolio_callback(element_id){
    var zayvalenie_id = getZayavlenieId(element_id);
    var file_id = getFileId(element_id);
    saveIspytanie(zayvalenie_id,file_id,'portfolio',element_id);
}

function select_var_isp2_callback(element_id){
    var zayvalenie_id = getZayavlenieId(element_id);
    var file_id = getFileId(element_id);
    saveIspytanie(zayvalenie_id,file_id,'var_isp2',element_id);
}

function select_var_isp3_callback(element_id){
    var zayvalenie_id = getZayavlenieId(element_id);
    var file_id = getFileId(element_id);
    saveIspytanie(zayvalenie_id,file_id,'var_isp3',element_id);
}

function select_prezentatsiya(element_id){
    var zayvalenie_id = getZayavlenieId(element_id);
    var file_id = getFileId(element_id);
    saveIspytanie(zayvalenie_id,file_id,'prezentatsiya',element_id);
}

function select_ik_callback(element_id){
    var zayvalenie_id = getZayavlenieId(element_id);
    var file_id = getFileId(element_id);
    saveIspytanie(zayvalenie_id,file_id,'informacionnaja_karta',element_id);
}

/**
 *
 * @param tip -  тип испытания  (портфолио, вариативное испытание 2 и 3, презентация) [portfolio,var_isp2,var_isp3,prezentatsiya]
 * @param zayavlenie_id - id заявление на аттестацию
 * @param element_id - id инпута,икоторый содержит id файла
 */
function saveIspytanie(zayavlenie_id,file_id,tip,element_id){
    briop_ajax({
        url: '/attestaciya/save-ispytanie',
        data: {
            file_id: file_id,
            zayavlenie_id: zayavlenie_id,
            tip: tip
        },
        done: function(answer){
            if (answer.result == 'success'){
                $('#'+element_id).files2('set_caption','Изменить файл');
                if (tip == 'portfolio'){
                    $('#portfolio'+zayavlenie_id).html(answer.html);
                    var file = $('#portfolio'+zayavlenie_id).find('.file_item');

                }
                if (tip == 'var_isp2' || tip == 'var_isp3'){
                    $('#var_isp'+zayavlenie_id).html(answer.html);
                    var file = $('#var_isp'+zayavlenie_id).find('.file_item');
                }
                if (tip == 'prezentatsiya'){
                    $('#prezentatsiya'+zayavlenie_id).html(answer.html);
                    var file = $('#prezentatsiya'+zayavlenie_id).find('.file_item');
                }
                if (tip == 'informacionnaja_karta'){
                    $('#informacionnaja_karta'+zayavlenie_id).html(answer.html);
                    var file = $('#informacionnaja_karta'+zayavlenie_id).find('.file_item');
                }
                file.addClass('btn btn-link link-btn');
            }
        }
    })
}