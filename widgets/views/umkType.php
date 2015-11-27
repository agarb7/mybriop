<?php
\app\assets\UmkTypesAsset::register($this);

use app\widgets\Files2Widget;
use yii\helpers\Html;

echo '<span class="inline-block vtop">
            <label for="type_'.$params['id'].'">тип</label><br>
            '.Html::dropDownList('type_'.$params['id'],null,['1'=>'файл','2'=>'ссылка'],['id'=>'type_'.$params['id'],'class'=>'form-control','onchange'=>'change_umk_type(\''.$params['id'].'\')']).'
        </span>
        <span class="inline-block" style="width:1em"></span>
        <span class="inline-block vtop hidden" id="umk_url_block'.$params['id'].'">
            <label for="umk_url'.$params['id'].'">Сслыка не ресурс</label>
            <input type="text" id="umk_url'.$params['id'].'" value=""  class="form-control">
        </span>
        <span class="inline-block vbottom" id="umk_file_block'.$params['id'].'">
             '.Files2Widget::widget(['id'=>$params['id']]).'
        </span>';