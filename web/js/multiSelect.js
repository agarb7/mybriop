function change_ms(elem_id){
    $('#mscont'+elem_id).toggleClass('hidden')
}

function parse_fio(str){
  var comma_index = str.indexOf(',');
  var new_str = '';
  if (comma_index != -1)
    new_str = str.substr(0,comma_index);
  else
      new_str = str;
  return new_str;
}

function change_checks(elem,id){
    var items = $('.checks'+id+':checked');
    var arr = [];
    var count= 0;
    for(var item in items){
        if (items[item].type == 'checkbox'){
            arr[arr.length] = parse_fio($(items[item]).parent('label').text());
            count++;
        }
    }
    if (count == 0) $('#mstext'+id).text('Выберите преподавателя');
    else if (count<=2)
        $('#mstext'+id).text(arr.join(','));
    else
        $('#mstext'+id).text('Выбрано '+count+' преподавателя');
    $('#mstext'+id).attr('title',arr.join(','));
}

function resetms(elem_id){
    $('#mstext'+elem_id).text('Выберите преподавателя');
    $('.checks'+elem_id).attr('checked',false);
    $('#mscont'+elem_id).addClass('hidden')
}

function ms_init_data(data,elem_id){
    resetms(elem_id);
    var data_array = eval('['+data+']');
    $('.checks'+elem_id).each(function(){
        if (data_array.indexOf(parseInt($(this).val())) != -1){
          $(this).prop('checked',true);
        }
    });
    change_checks(null,elem_id);
    return data_array;
}

function get_checked(elem_id){
    var res = [];
    $('.checks'+elem_id+':checked').each(function(){
        res[res.length] = $(this).val();
    });
    return res;
}