<?php

\app\assets\KimTypeAsset::register($this);

use app\widgets\Files2Widget;
use yii\helpers\Html;
use app\globals\KursGlobals;

echo '
    <span class="inline-block vtop">
        <label for="type_'.$params['id'].'">тип</label><br>
        '.Html::dropDownList('type_'.$params['id'],null, KursGlobals::kim_types(),['id'=>'type_'.$params['id'],'class'=>'form-control','onchange'=>'onchange_kim_type(\''.$params['id'].'\')']).'
    </span>
    <span class="inline-block" style="width:1em"></span>
    <span class="inline-block vtop hidden  kim_type_block'.$params['id'].'" id="'.$params['id'].'_url_block">
        <label for="'.$params['id'].'_url">Сслыка не ресурс</label>
        <input type="text" id="'.$params['id'].'_url" value=""  class="form-control">
    </span>
    <span class="inline-block vbottom kim_type_block'.$params['id'].'" id="'.$params['id'].'_file_block">
        '.Files2Widget::widget(['id'=>$params['id']]).'
    </span>
    <span class="inline-block vbottom hidden kim_type_block'.$params['id'].'" id="'.$params['id'].'_text_block">
        <label for="'.$params['id'].'_text">Текст</label>
        <textarea id="'.$params['id'].'_text" style="width:30em" class="form-control"></textarea>
    </span>';