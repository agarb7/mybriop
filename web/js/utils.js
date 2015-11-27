/*
config - объект js.
Поля config :
type - определяет ти  алерта (success,warning,info,danger)
position - местоположение алерат top - наверху, bottom - снизу, middle - по центру.
При этом свойство position задается как fixed
*/
function bsalert(message,type,position){

    this.id = 'bsalert'+Date.now();

    this.class = type ? type : 'success';

    this.position = position ? position : 'top';

    message = message || '';

    $('body').append(
        '<div id="'+this.id+'" class="alert bs-alert alert-'+this.class+' bsalert-'+this.position+' alert-dismissible" role="alert">\
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
            '+message+'\
        </div>'
    );

    if (this.position == 'middle'){
        var height = $('#'+this.id).height()+30;
        $('#'+this.id).attr('style','margin-top: -'+height+'px');
    }

    $('#'+this.id).delay(4000).fadeOut(300);
}