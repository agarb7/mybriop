/**
 *
 * @param {data}
 * @param {fail}
 * @param {finally}
 * @param {url}
 * @this {req}
 */
function briop_ajax(req){
    showLoader();

    req.data = req.data || {};

    req.fail = req.fail || false;
    req.finally = req.finally || false;

    var request = $.ajax({
        url: req.url,
        type: "POST",
        data: req.data,
        dataType: "json"
    });
    request.done(function(answer){
        console.log(answer);
        req.done(answer);
        hideLoader();
    });
    request.fail(function(jqXHR, textStatus){
        bsalert("Ошибка подключения к серверу.",'danger');
        console.log(request.responseText);
        hideLoader();
        if (req.fail) req.fail();
    });
    request.always(function(){
        if (req.finally) req.finally();
    })
}

function showLoader(){
    if (!$('#ajax_loader').length)
        $('body').append('<div id="ajax_loader" class="loader hidden"></div>');
    $('#ajax_loader').removeClass('hidden')
}

function hideLoader(){
    $('#ajax_loader').addClass('hidden');
}