function onSelect3NazvanieKeyUp(IdInput,value,event){
    if (value != '')
        $('#'+IdInput).select2('val','');
}

function showSelect3Nazvanie(idInputId,nazvanieInputId,element){
    $select = $('#'+idInputId).next('.select2');
    $select.addClass('hidden');
    $('#'+idInputId).select2('val','');
    $('#'+nazvanieInputId).removeClass('hidden');
    var parent = $(element).parent();
    parent.find('.show-nazvanie-span').addClass('hidden');
    parent.find('.show-id-span').removeClass('hidden');
}

function showSelect3Id(idInputId,nazvanieInputId,element){
    $('#'+nazvanieInputId).addClass('hidden');
    $('#'+nazvanieInputId).val('');
    $('#'+idInputId).next('.select2').removeClass('hidden');
    var parent = $(element).parent();
    parent.find('.show-id-span').addClass('hidden');
    parent.find('.show-nazvanie-span').removeClass('hidden');
}