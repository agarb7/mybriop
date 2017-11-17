/**
 * Created by asv on 22.10.2017.
 */

function vyborPodrazdelenija() {
    var value = $("#podrazdelenie-id").val();
    if(value){
        showLoader();
        $.post("./sostav?pid="+value,
            function( data ) {
                $( "div#sostav-podrazdelenija" ).html( data );
                hideLoader();
            });
    }else{
        $("div#sostav-podrazdelenija").empty();
    };
}

function tip_dogovora() {
    var tip = $("#sotrudnik-tipdogovora").val();
    if (tip == 'gph') $("#trud").hide();
        else $("#trud").show();
}
