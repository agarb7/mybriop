<?php

\app\assets\MultiSelectAsset::register($this);

echo '<span class="slink" id="mstext'.$params['id'].'" onclick="change_ms(\''.$params['id'].'\')">Выберите преподавателей</span>
        <div class="relative">
            <div class="ms-box hidden" id="mscont'.$params['id'].'">
                <ul style="padding: 0.2em;">';
                foreach ($params['data'] as $k=>$v){
                    echo '<li class="checkbox"><label><input class="checks'.$params['id'].'" onchange="change_checks(this,\''.$params['id'].'\')" type="checkbox" value="'.$k.'">'.$v.'</label></li>';
                }
echo                '</ul>
            </div>
      </div>';
