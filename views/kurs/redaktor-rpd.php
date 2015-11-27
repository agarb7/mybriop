<?php

use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\globals\RpdGlobals;
use app\widgets\KimTypeWidget;

\app\assets\RpdAsset::register($this);

$this->title = 'Редактор РПД';

echo '<h3>'.$podrazdel['nazvanie'].'</h3>';

$form = ActiveForm::begin([
    'id' => 'podrazdel-form'
]);

echo $form->field($podrazdel, 'aktualnost')->textarea(['class'=>'form-control']);

echo $form->field($podrazdel, 'cel')->textarea(['class'=>'form-control']);

echo $form->field($podrazdel, 'zadachi')->textarea(['class'=>'form-control']);

echo $form->field($podrazdel, 'planiruemye_rezultaty')->textarea(['class'=>'form-control']);

echo $form->field($podrazdel, 'mesto_discipliny_v_strukture_programmy')->textarea(['class'=>'form-control']);

echo '<h4>Организационно-педагогические условия</h4>';

echo $form->field($podrazdel, 'informacionnye_usloviya')->textarea(['class'=>'form-control']);

echo $form->field($podrazdel, 'uchebnometodicheskie_usloviya')->textarea(['class'=>'form-control']);

echo $form->field($podrazdel, 'kadrovye_usloviya')->textarea(['class'=>'form-control']);

echo $form->field($podrazdel, 'materialnotehnicheskie_usloviya')->textarea(['class'=>'form-control']);

echo $form->field($podrazdel, 'literatura')->textarea(['class'=>'form-control']);

echo Html::input('hidden','razdel_nomer',$nomer,['id'=>"razdel_nomer"]);
echo Html::input('hidden','podrazdel_nomer',$podrazdel['nomer'],['id'=>"podrazdel_nomer"]);

echo '<h4>Содержание</h4>';

echo '<table id="topics_table" class="topics-table">';

echo RpdGlobals::get_podrazdel_row(current($soderzhanie));

echo '</table>';

echo '<p>&nbsp;</p>';

echo '<p><label for="status_discipliny"><input onchange="change_podpis('.$podrazdel['id'].')" type="checkbox" id="status_discipliny" '.($podrazdel['status']==1 ? 'checked' : '').'>&nbsp;Подписать</label></p>';

echo '<p><a class="btn btn-primary" target="blank" href="/pdf/rpd?id='.$podrazdel['id'].'">Печать</a></p>';

echo '<p>'.Html::button('Сохранить', ['id'=>'rpd_smb_btn','class' => 'btn btn-primary','clicked'=>'false']).'</p>';

ActiveForm::end();


echo '<div class="alert center" style="display: none" id="alert-div"></div>';

echo '
<div class="podrazdel-form hidden" id="add_theme_form">
    <div class="form-group">
        <label for="theme_name">Наименование темы</label>
        <input class="form-control" type="text" value="" id="theme_name">
        <input type="hidden" id="podrazdel_id" value="">
     </div>
     <div class="form-group">
        <label for="vid_rabot">Вид занятий</label>
         '.Html::dropDownList('vid_rabot',null,$vidy_rabot,['id'=>'vid_rabot','class'=>'form-control']).'
     </div>
     <div class="form-group">
         <label for="sotrudniki">Преподаватель</label>
         '.Html::dropDownList('sotrudniki',null,$sotrudniki,['id'=>'sotrudniki','class'=>'form-control']).'
     </div>
     <div class="form-group">
         <label for="soderzhanie">Аннотация</label>
         <textarea class="form-control" id="soderzhanie"></textarea>
     </div>
     <div>
        <span class="inline-block vtop">
         <div class="form-group">
            <label for="theme_chasy">Кол-во часов</label>
             <input class="form-control" size="3"  style="width:4.5em" min="2" type="number" step="2" id="theme_chasy" value=""/>
         </div>
         </span>
         <span class="inline-block" style="width:1em"></span>
         <span class="inline-block vtop">
         <div class="form-group">
            <label for="theme_week">Неделя</label>
            '.Html::dropDownList('theme_week',null,$weeks,['id'=>'theme_week','class'=>'form-control']).'
         </div>
         </span>
     </div>
     <button class="btn btn-default" type="button" onclick="save_theme()">Сохранить</button> <span onclick="hide_form(\'add_theme_form\')" class="slink">отмена</span>
</div>';

echo '
<div class="podrazdel-form top_arrow hidden" id="edit_theme_form">
    <div class="form-group">
        <label for="theme_edit_name">Наименование темы</label>
        <input class="form-control" type="text" value="" id="theme_edit_name">
        <input type="hidden" id="theme_id" value="">
     </div>
     <div class="form-group">
        <label for="vid_edit_rabot">Вид занятий</label>
         '.Html::dropDownList('vid_edit_rabot',null,$vidy_rabot,['id'=>'vid_edit_rabot','class'=>'form-control']).'
     </div>
     <div class="form-group">
         <label for="sotrudniki_edit">Преподаватель</label>
         '.Html::dropDownList('sotrudniki_edit',null,$sotrudniki,['id'=>'sotrudniki_edit','class'=>'form-control']).'
     </div>
     <div class="form-group">
         <label for="soderzhanie_edit">Аннотация</label>
         <textarea class="form-control" id="soderzhanie_edit"></textarea>
     </div>
     <div>
         <span class="inline-block vtop">
         <div class="form-group">
            <label for="theme_edit_chasy">Кол-во часов</label>
            <input class="form-control" size="3"  style="width:4.5em" min="2" type="number" step="2" id="theme_edit_chasy" value=""/>
         </div>
         </span>
          <span class="inline-block" style="width:1em"></span>
         <span class="inline-block vtop">
         <div class="form-group">
            <label for="theme_edit_week">Неделя</label>
            '.Html::dropDownList('theme_edit_week',null,$weeks,['id'=>'theme_edit_week','class'=>'form-control']).'
         </div>
         </span>
     </div>
     <button class="btn btn-default" type="button" onclick="save_edit_theme()">Сохранить</button> <span onclick="hide_form(\'edit_theme_form\')" class="slink">отмена</span>
</div>';

echo '
<div class=" cc-form podrazdel-form hidden" id="add_cc_form">
    <div class="form-group">
        <label for="forma_kontrolya_temi">Форма контроля</label>
        '.Html::dropDownList('forma_kontrolya_temi',null,$kf_temi,['id'=>'forma_kontrolya_temi','class'=>'form-control']).'
        <input type="hidden" value="" id="theme_kf_id">
    </div>
     <button class="btn btn-default" type="button" onclick="save_kf()">Сохранить</button> <span onclick="hide_form(\'add_cc_form\')" class="slink">отмена</span>
</div>';

echo '
<div class="podrazdel-form top_arrow hidden" id="add_cc_edit_form">
   <div class="form-group">
        <label for="forma_kontrolya_temi_edit">Форма контроля</label>
        '.Html::dropDownList('forma_kontrolya_temi_edit',null,$kf_temi,['id'=>'forma_kontrolya_temi_edit','class'=>'form-control']).'
        <input type="hidden" value="" id="theme_kf_edit_id">
    </div>
     <button class="btn btn-default" type="button" onclick="save_edit_kf()">Сохранить</button> <span onclick="hide_form(\'add_cc_edit_form\')" class="slink">отмена</span>
</div>';

echo '
<div class=" cc-form podrazdel-form hidden" id="add_kim_form">
    <div class="form-group">
        <label for="kim_opisanie">Описание</label>
        <textarea class="form-control" id="kim_opisanie"></textarea>
        <input type="hidden" id="theme_kim_id" value="">
    </div>
    <div class="form-group">
        '.KimTypeWidget::widget(['params'=>['id'=>'kim']]).'
    </div>
     <button class="btn btn-default" type="button" onclick="save_kim()">Сохранить</button> <span onclick="hide_form(\'add_kim_form\')" class="slink">отмена</span>
</div>';


echo '
<div class="podrazdel-form top_arrow hidden" id="edit_kim_form">
    <div class="form-group">
        <label for="kim_edit_opisanie">Описание</label>
        <textarea class="form-control" id="kim_edit_opisanie"></textarea>
        <input type="hidden" id="kim_edit_id" value="">
    </div>
    <div class="form-group">
        '.KimTypeWidget::widget(['params'=>['id'=>'edit_kim']]).'
    </div>
     <button class="btn btn-default" type="button" onclick="save_kim_edit()">Сохранить</button> <span onclick="hide_form(\'edit_kim_form\')" class="slink">отмена</span>
</div>';

echo '
<div class=" umk-form podrazdel-form hidden" id="add_podrazdel_umk_form">
    <div class="form-group">
        <label for="podrazdel_umk_opisanie">Описание</label>
        <textarea class="form-control" id="podrazdel_umk_opisanie"></textarea>
        <input type="hidden" id="umk_podrazdel_id" value="">
    </div>
    <div class="form-group">
        '. \app\widgets\UmkTypeWidget::widget(['params'=>['id'=>'podrazdel_umk']]).'
    </div>

     <button class="btn btn-default" type="button" onclick="save_podrazdel_umk()">Сохранить</button> <span onclick="hide_form(\'add_podrazdel_umk_form\')" class="slink">отмена</span>
</div>';

echo '
<div class="podrazdel-form top_arrow hidden" id="edit_umk_form">
    <div class="form-group">
        <label for="umk_edit_opisanie">Описание</label>
        <textarea class="form-control" id="umk_edit_opisanie"></textarea>
        <input type="hidden" id="umk_id" value="">
    </div>
     <div class="form-group">
        '. \app\widgets\UmkTypeWidget::widget(['params'=>['id'=>'umk_edit']]).'
    </div>
     <button class="btn btn-default" type="button" onclick="save_edit_umk()">Сохранить</button> <span onclick="hide_form(\'edit_umk_form\')" class="slink">отмена</span>
</div>';

echo '
<div class=" cc-form podrazdel-form hidden" id="add_podrazdel_kim_form">
    <div class="form-group">
        <label for="podrazdel_kf_kim_opisanie">Описание</label>
        <textarea class="form-control" id="podrazdel_kf_kim_opisanie"></textarea>
        <input type="hidden" id="podrazdel_kim_id" value="">
    </div>
    <div class="form-group">
        '.KimTypeWidget::widget(['params'=>['id'=>'podrazdel_kim']]).'
    </div>
     <button class="btn btn-default" type="button" onclick="save_podrazdel_kim()">Сохранить</button> <span onclick="hide_form(\'add_podrazdel_kim_form\')" class="slink">отмена</span>
</div>';

echo '<div class="loader hidden"></div>';