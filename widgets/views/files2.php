<?php

use \app\helpers\Html;

\app\widgets\FileAsset::register($this);

$files2_options = 'id: "'.$id.'", name: "'.$name.'"';

if ($file_id) $files2_options .= ', file_id: '.$file_id;
if ($select_callback) $files2_options .= ', select_callback: '.$select_callback;
if ($caption) $files2_options .= ', caption: "'.$caption.'"';

$this->registerJs('$(function(){  $("#'.$id.'").files2({'.$files2_options.'}); })');

$ini_max_size=ini_get('upload_max_filesize');
$MAX_UPLOAD_SIZE=$ini_max_size;
$MAX_UPLOAD_BYTES=(int)$ini_max_size;
switch (substr($ini_max_size, -1)){case 'G': $MAX_UPLOAD_BYTES *= 1024; case 'M': $MAX_UPLOAD_BYTES *= 1024; case 'K': $MAX_UPLOAD_BYTES *= 1024;}

$this->registerJs('MAX_UPLOAD_SIZE = "'.$MAX_UPLOAD_SIZE.'"');
$this->registerJs('MAX_UPLOAD_BYTES = '.$MAX_UPLOAD_BYTES);

echo Html::tag('div','Ошибка',['id'=>$id]+$options);

//echo $content = Html::button('Выбрать файл',[
//    'class'=>'choose-file-btn form-control btn',
//    'id' => 'btn'.$params['id'],
//    'value' => '1',
//    'type' => 'button',
//    'onclick' => "choose_file('$params[id]')"
//]);
//echo '<input type="hidden" id="'.$params['id'].'" name="'.$params['name'].'">';
