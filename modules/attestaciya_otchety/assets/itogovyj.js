$(function(){
    change_url();
})

function change_url(){
    var vp = $('#periods option:selected').val();
    var dolzhnost = $('#dolzhnosti option:selected').val();
    var url = window.location.href + '?vp=' + vp + '&d=' + dolzhnost;
    $('#report_btn').attr('href',url);
    //window.open(url, '_blank');
}