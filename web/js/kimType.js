function onchange_kim_type(base_id){
    var type = $('#type_'+ base_id +' option:selected').val();
    $('.kim_type_block'+base_id).addClass('hidden');
    switch (type) {
        case '1':
            $('#'+base_id+'_file_block').removeClass('hidden');
            break;
        case '2':
            $('#'+base_id+'_url_block').removeClass('hidden');
            break;
        case '3':
            $('#'+base_id+'_text_block').removeClass('hidden');
            break;
    }
}
