$(function(){
    change_url();
    $('#spisok').select2();
})

function change_url(){
    var vp = $('#periods option:selected').val();
    var dolzhnost = $('#dolzhnosti option:selected').val();
    var zayvlenie = $('#spisok option:selected').val();
    var params = [];
    if (vp){
        params.push('vp=' + vp);
    }
    if (dolzhnost){
        params.push('d=' + dolzhnost);
    }
    if (zayvlenie){
        params.push('z=' + zayvlenie);
    }
    var url = window.location.href + '?' + params.join('&');
    $('#report_btn').attr('href',url);
    //window.open(url, '_blank');+
}

