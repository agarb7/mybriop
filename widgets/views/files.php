<?php

use \app\helpers\Html;

\app\widgets\FileAsset::register($this);


echo $content = Html::button($params['btn-text'],[
    'class'=>'choose-file-btn form-control btn',
    'id' => 'btn'.$params['id'],
    'value' => '1',
    'type' => 'button',
    'onclick' => "choose_file('$params[id]')"
]);
echo '<input type="hidden" id="'.$params['id'].'" name="'.$params['name'].'">';


$files = '';
foreach ($params['files'] as $k=>$v) {
    if (isset($model) && $model->$params['pureAttribute'] == $v['id']) $is_checked = true;
    else $is_checked = false;
    $files.=\app\globals\ApiGlobals::file_row('radio',$v['vneshnee_imya_fajla'],$v['id'],$params['id'],$is_checked);
}
if (!$files) $files = '\\';

$files_from = '<div class="dark_fon hidden" tabindex="10" onkeydown="darkKeyDown(event,\\\''.$params['id'].'\\\')" id="'.$params['id'].'_form">\
         <div class="add_files_form">\
              <div class="file_add_section">\
                <form id="add_files'.$params['id'].'"  method="POST" action="" enctype="multipart/form-data">\
                    <label for="'.$params['id'].'_input" id="lable'.$params['id'].'" class="add-file-button btn btn-primary">Загрузить файл</label>\
                    <input onchange="change_file'.$params['id'].'()" type="file" class="file-input" id="'.$params['id'].'_input" name="'.$params['id'].'_input[]">\
                    <div class="myprogress hidden" id="progress'.$params['id'].'">\
                        <div class="bar"></div>\
                        <div class="percent">0%</div >\
                    </div>\
                </form>\
              </div>\
              <div class="center_files_form">\
                <div class="file-headers">\
                 <span class="file-name-header">Имя файла</span>\
                </div>\
                <div class="file-list">\
                    '.$files.'
                </div>\
                <div class="file-bottom">\
                    <span class="btn btn-primary" onclick="select_file'.$params['id'].'(\\\''.$params['id'].'\\\',\\\''.$params['name'].'\\\')">Продолжить</span>\
                    <span class="slink" onclick="hide_add_file(\\\''.$params['id'].'\\\')">Отмена</span>\
                </div>\
              </div>\
         </div>\
       </div>';

echo '';
$ini_max_size=ini_get('upload_max_filesize');
$MAX_UPLOAD_SIZE=$ini_max_size;
$MAX_UPLOAD_BYTES=(int)$ini_max_size; switch (substr($ini_max_size, -1)){case 'G': $MAX_UPLOAD_BYTES *= 1024; case 'M': $MAX_UPLOAD_BYTES *= 1024; case 'K': $MAX_UPLOAD_BYTES *= 1024;}

//to do
$polzovatel_id = 1;// \app\globals\ApiGlobals::getPolzovatelId();
if (!$polzovatel_id)
    throw new \Exception;

echo '
     <script>

        $(function (){
            $("body").append(\''.$files_from.'\');
        });

        function select_file'.$params['id'].'(form_id,input_id){
            var file_id = $(".file-chbx[name=\'file_checkbox_"+form_id+"\']:checked").val();
            if (file_id){
            $("#"+input_id).val(file_id);
            $("#btn"+form_id).text($("#file-name"+file_id).text());
            }
            hide_add_file(form_id);
            //alert(file_id);
        }

        function choose_file(form_id){
            $("#"+form_id+"_form").removeClass("hidden");
            $("#"+form_id+"_form").focus();
        }

        function hide_add_file(form_id){
            $("#"+form_id+"_form").addClass("hidden");
        }

         function check_file(id){
            $(".file-row").removeClass("selected-file");
            $("#file_row"+id).addClass("selected-file");
         }

         function change_file'.$params['id'].'(){
            $("#lable'.$params['id'].'").addClass("hidden");
            $("#progress'.$params['id'].'").removeClass("hidden");
            var bar = $("#progress'.$params['id'].' .bar");
            var percent = $("#progress'.$params['id'].' .percent");
            var status = $("#status");
            $("#add_files'.$params['id'].'").ajaxSubmit({
                url: "/files/upload",
                type: "post",
                dataType: "json",
                data: {
                    "ajax_query": "upload_file",
                    "user_id":'.$polzovatel_id.',
                    "widget_id":"'.$params['id'].'"
                },
                beforeSubmit: function(){
                    var f=$("#'.$params['id'].'_input")[0].files;
                    if(f && f[0] && f[0].size>'.$MAX_UPLOAD_BYTES.'){
                        alert("Загружаемый файл привышает допустимый размер ('.$MAX_UPLOAD_SIZE.')");
                        $("#lable'.$params['id'].'").removeClass("hidden");
                        $("#progress'.$params['id'].'").addClass("hidden");
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
                    $("#lable'.$params['id'].'").removeClass("hidden");
                    $("#progress'.$params['id'].'").addClass("hidden");
                    alert("Файл не загружен");
                },
                success: function(data) {
                    var percentVal = "100%";
                    bar.width(percentVal);
                    percent.html(percentVal);
                    $("#lable'.$params['id'].'").removeClass("hidden");
                    $("#progress'.$params['id'].'").addClass("hidden");
                    $("#'.$params['id'].'_form .file-list").append(data.html);
                    console.log(data);
                },
                complete: function(xhr) {
                    $("#'.$params['id'].'").val("");
                    status.empty();
                    var percentVal = "0%";
                    bar.width(percentVal);
                    percent.html(percentVal);
                }
            });
        }
     </script>';