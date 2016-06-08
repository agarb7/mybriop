function open_report(){
    var vp = $('#periods option:selected').val();
    var url = window.location.href + '?vp=' + vp;
    $('#report_btn').attr('href',url);
    //window.open(url, '_blank');
}