function change_umk_type(umk_id){
    var type = $('#type_'+umk_id+' option:selected').val();
    if (type == 1){
        $('#umk_url_block'+umk_id).addClass('hidden');
        $('#umk_file_block'+umk_id).removeClass('hidden');
    }
    else{
        $('#umk_url_block'+umk_id).removeClass('hidden');
        $('#umk_file_block'+umk_id).addClass('hidden');
    }
}
