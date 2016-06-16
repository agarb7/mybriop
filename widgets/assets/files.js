/**
 * Created by macbook22 on 30.08.15.
 */
function darkKeyDown(event){
    if (event.keyCode === 27){
        hide_add_file();
    }
}

is_loading = false;

initial_objects = [];

callbacks = {};
captions = {};

(function( $ ){

    var settings = {};
    var files = {};
    var select_callback = false;

    var methods = {
        init : function( options ) {
            settings = $.extend( {
                'id'      : 'files'+(new Date).getTime()+Math.random()*1000,
                'name'    : 'select_button',
                'file_id' : -1,
                'select_callback' : false,
                'caption' : false
            }, options);

            $(this).html('<button class="choose-file-btn form-control btn" ' +
                'value="1" type="button"></button> ' +
                '<input type="hidden" name="'+ settings.name +'" id="">'
            );

            //console.log($(this).html());

            if (settings.select_callback)
                callbacks[settings.id] = settings.select_callback;

            var is_exists = $('#files_table').length > 0 ? true : false;
            if (!is_exists && !is_loading) {
                is_loading = true;
                briop_ajax({
                    url: '/files/get-user-files',
                    done: function (data) {
                        if ($('#files_table').length == 0)
                            $('body').append(data.html);
                        files = data.files;
                        //console.log(initial_objects);
                        for (var i in initial_objects){
                            if (initial_objects.hasOwnProperty(i)){
                                initial_objects[i].object.files2('set_file',initial_objects[i].file_id);
                                if (initial_objects[i].caption)
                                    initial_objects[i].object.files2('set_caption',initial_objects[i].caption);
                            }
                        }
                    }
                });
            }

            if ($('#files_table').length > 0) {
                $(this).files2('set_file', settings.file_id);
            }
            else
                initial_objects.push({object: $(this), file_id: settings.file_id,caption: settings.caption});


            $(this).find('button').click(function(){
                $(this).parent().files2('choose_files');
            });

            if (settings.caption) $(this).files2('set_caption',settings.caption);
        },
        choose_files: function(){
            var id = $(this).attr('id');
            var file_id = $(this).find('input').val();
            $(".file-chbx").prop('checked',false);//сбрасываем все radio
            $(".file-row").removeClass("selected-file");
            if (file_id) {//выделяем один, если файл уже выбран
                $("#file_row"+file_id).addClass("selected-file");
                $('#file_checkbox_'+file_id).prop('checked',true);
            }
            $('#element_id').val(id);
            $("#files_table").removeClass("hidden");
            $("#files_table").focus();
        },
        select_file: function(file){
            var button = $(this).find('button');
            var input = $(this).find('input');
            button.text(file.file_name);
            input.val(file.file_id);
        },
        set_file: function(file_id){
            if (file_id > 0 ){
                $(this).files2('set_caption',$('#file-name'+file_id).text());
                $(this).find('input').val(file_id);
            }
            else{
                $(this).files2('set_caption','Выберите файл');
                $(this).find('input').val('');
            }
        },
        get_file_name: function(){
            var name = $(this).find('button').text();
            var id = $(this).find('input').val();
            if (id) return name;
            else return '';
        },
        get_file_id: function(){
            var id = $(this).find('input').val();
            if (id) return id;
            else return '';
        },
        set_caption: function(caption){
            $(this).find('button').text(caption);
        },
        select_callback: function(){
            if (select_callback){
                select_callback();
            }
        }
    };

    $.fn.files2 = function( method ) {

        // логика вызова метода
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Метод с именем ' +  method + ' не существует для jQuery.files2' );
        }
    };

    return this;

})( jQuery );

function select_file(){
    var file_id = $(".file-chbx:checked").val();
    if (file_id){
        var file_name = $("#file-name"+file_id).text();
        var element_id = $('#element_id').val();
        $('#'+element_id).files2('select_file',{file_id: file_id,file_name: file_name});
        if (callbacks[element_id]) callbacks[element_id](element_id);
    }
    hide_add_file();
}

function hide_add_file(){
    $("#files_table").addClass("hidden");
}

function check_file(id){
    $(".file-row").removeClass("selected-file");
    $("#file_row"+id).addClass("selected-file");
}

function change_file(){
    $("#load_file_lable").addClass("hidden");
    $("#progress").removeClass("hidden");
    var bar = $("#progress .bar");
    var percent = $("#progress .percent");
    var status = $("#status");
    $("#add_files").ajaxSubmit({
        url: "/files/upload",
        type: "post",
        dataType: "json",
        data: {
            "ajax_query": "upload_file"
        },
        beforeSubmit: function(){
            var f=$("#load_file")[0].files;
            if(f && f[0] && f[0].size> MAX_UPLOAD_BYTES){
                alert("Загружаемый файл привышает допустимый размер (" + MAX_UPLOAD_SIZE + ")");
                $("#load_file_lable").removeClass("hidden");
                $("#progress").addClass("hidden");
                return false;
            }
        },
        beforeSend: function() {
            status.empty();
            var percentVal = "0%";
            bar.width(percentVal);
            percent.html(percentVal);
            //$("#news_files").addClass("hidden");
        },
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete + "%";
            bar.width(percentVal);
            percent.html(percentVal);
            //console.log(percentVal, position, total);
        },
        error:function (e,t){
            console.log(e.responseText);
            $("#load_file_lable").removeClass("hidden");
            $("#progress").addClass("hidden");
            alert("Файл не загружен");
        },
        success: function(data) {
            var percentVal = "100%";
            bar.width(percentVal);
            percent.html(percentVal);
            $("#load_file_lable").removeClass("hidden");
            $("#progress").addClass("hidden");
            $("#files_table .file-list").append(data.html);
            console.log(data);
        },
        complete: function(xhr) {
            //$("#'.$params['id'].'").val("");
            status.empty();
            var percentVal = "0%";
            bar.width(percentVal);
            percent.html(percentVal);
        }
    });
}
