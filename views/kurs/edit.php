<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\widgets\FilesWidget;
use app\widgets\Files2Widget;
use app\widgets\KimTypeWidget;
use app\globals\ApiGlobals;
use app\globals\KursGlobals;
use \app\enums\StatusProgrammyKursa;

\app\assets\KursAsset::register($this);


$this->title = 'Курс '.$kursModel['nazvanie'];

$status = $kursModel['status_programmy'];

$form = ActiveForm::begin([
    'id' => 'kursModel-form'
]);

$drawField = function ($fieldName) use ($status,$kursModel,$form){
                $result = '';
                if ($status == StatusProgrammyKursa::REDAKTIRUETSYA)
                    $result = $form->field($kursModel, $fieldName)->textarea(['class'=>'kurs_field form-control']);
                else {
                    $result  = Html::tag('div',$kursModel->getAttributeLabel($fieldName),['class'=>'bold']);
                    $result .= Html::tag('p', $kursModel[$fieldName]);
                }
                return $result;
            };

/**
 * @var \app\models\Kurs\KursRecord $kursModel
 */

echo '<h3>'.$kursModel['nazvanie'].'</h3>';
echo '<p><b>Аннотация</b></p>';
echo $kursModel['annotaciya'];
echo '<h4>Пояснительная записка</h4>';
echo Html::activeHiddenInput($kursModel, 'id');
//echo Html::activeHiddenInput($kursModel, 'nazvanie');
//echo Html::activeHiddenInput($kursModel, 'annotaciya');
echo '<textarea class="hidden" name="ghghghghghghgh">dsfsdfs</textarea>';
echo $drawField('aktualnost');
echo $drawField('cel');
echo $drawField('zadachi');

if ($kursModel['tip'] != 'pk') {
    if ($kursModel['tip'] == 'po') {
        $drawField('harakteristika_novoj_kvalifikacii');
       // echo $form->field($kursModel, 'trebovaniya_k_urovnyu_podgotovki')->textarea(['class'=>'kurs_field form-control']);
    }
    if ($kursModel['tip'] == 'pp') {
        echo $drawField('harakteristika_novogo_vida_deyatelnosti');
    }
    echo $drawField('sostaviteli');
    echo $drawField('recenzenti');
}

echo $drawField('planiruemye_rezultaty');

echo '<h4>Организационно-педагогические условия:</h4>';
echo $drawField('informacionnye_usloviya');
echo $drawField('uchebnometodicheskie_usloviya');
echo $drawField('kadrovye_usloviya');
echo $drawField('tehnicheskie_usloviya');

if ($kursModel['tip']=='pk')
    echo $drawField('itogovaya_attestaciya');

echo Html::tag('p','<strong>Количество часов: </strong>'.$kursModel['raschitano_chasov']);

echo $drawField('rezhim_zanyatij');

if ($kursModel['tip']!='pk'){
    echo $drawField('forma_obucheniya');
    echo $drawField('itogovaya_attestaciya_tekst');
}

echo Html::tag('p','<strong>Количество слушателей: </strong>'.$kursModel['raschitano_slushatelej']);
echo '<p><strong>Категории слушателей: </strong>'.implode(', ', array_map(function ($entry) {
    return $entry['nazvanie'];
}, $kursModel['kategoriyaSlushatelyas'])).'</p>';

$plan = KursGlobals::get_uchebnii_plan_html($kug,$attestaciya);

$kug = KursGlobals::get_kug_html($kug,$attestaciya,$max_week_num);

//$kug_and_plan = \yii\globals\kurs\KursGlobals::get_kug_and_plan_html($kug,$attestaciya);

echo '<p style="color:#8e8e8e">Учебный план и календарный учебный график формируются автоматически на основе содержания разделов. Обновляется после нажатия на кнопку сохранить в конце редактора.</p>';

echo '<h4>Учебный план</h4>';

echo $plan;

echo '<h4>Календарный учебный график</h4>';

echo $kug;

echo '<h4>Содержание разделов, блоков тем/дисциплин, тем, занятий по часам</h4>';
//var_dump($podrazdels['r1']);die();

if ($status == StatusProgrammyKursa::REDAKTIRUETSYA)
    echo '<p><button onclick="add_razdel()" class="btn btn-primary" type="button">Добавить раздел</button></p>';

echo '
<div class="rk-form podrazdel-form hidden" id="add_razdel_form">
    <div class="form-group">
        <label for="razdel_nazvanie">Название</label>
        '.Html::dropDownList('razdel_nazvanie',null,$razdels,['id'=>'razdel_nazvanie','class'=>'form-control','onchange'=>'razdel_cmb_change(\'razdel_nazvanie\',1)']).'
        <input type="hidden" id="razdel_kurs_id" value="'.$kursModel['id'].'">
    </div>
    <div class="form-group hidden" id="add_razdel_nazvanie1">
        <label for="new_razdel_nazvanie">Название нового раздела</label>
        <input class="form-control" type="text" id="new_razdel_nazvanie" value="">
    </div>
    <div class="form-group">
        <label for="razdel_types">Тип</label>
        '.Html::dropDownList('razdel_types',null,$razdel_types,['id'=>'razdel_types','class'=>'form-control']).'
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_razdel()">Сохранить</button> <span onclick="hide_form(\'add_razdel_form\')" class="slink">отмена</span>
</div>';

echo '
<div class="rk-form podrazdel-form hidden" id="edit_razdel_form">
    <div class="form-group">
        <label for="edit_razdel_nazvanie">Название</label>
        '.Html::dropDownList('edit_razdel_nazvanie',null,$razdels,['id'=>'edit_razdel_nazvanie','class'=>'form-control','onchange'=>'razdel_cmb_change(\'edit_razdel_nazvanie\',2)']).'
        <input type="hidden" id="edit_razdel_id" value="">
    </div>
    <div class="form-group hidden" id="add_razdel_nazvanie2">
        <label for="new_edit_razdel_nazvanie">Название нового раздела</label>
        <input class="form-control" type="text" id="new_edit_razdel_nazvanie" value="">
    </div>
    <div class="form-group">
        <label for="edit_razdel_types">Тип</label>
        '.Html::dropDownList('edit_razdel_types',null,$razdel_types,['id'=>'edit_razdel_types','class'=>'form-control']).'
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_edit_razdel()">Сохранить</button> <span onclick="hide_form(\'edit_razdel_form\')" class="slink">отмена</span>
</div>';

echo '<table id="topics_table" class="topics-table">
       <thead>
        <tr>
            <td class="center" colspan="3">Наименование разделов (модулей) и тем</td>
        </tr>
       </thead>
       <tbody>
       ';

echo '<tr id="baz_tr" class="subhead section"><td class="center data" colspan="3">Базовая часть</td></tr>';
if (isset($podrazdels['baz']))
    foreach ($podrazdels['baz'] as $k=>$v) {
        echo KursGlobals::get_razdel_row($v,$status);
    }
echo '<tr id="baz_tr_footer" class="section_footer"><td class="center data" colspan="3"></td></tr>';
echo '<tr id="prof_tr" class="subhead section"><td colspan="3" class="center data">Профильная часть</td></tr>';
if (isset($podrazdels['prof']))
    foreach ($podrazdels['prof'] as $k=>$v) {
        echo KursGlobals::get_razdel_row($v,$status);
    }
echo '<tr id="prof_tr_footer" class="section_footer"><td class="center data" colspan="3"></td></tr>';

//<tr id="tr_before_attestaciya"><td colspan="3"></td></tr>

echo       '
            <tr id="attestaciya" class="section atr">
                <td class="action-td">
                    '. ($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                    '<div class="actions-control">
                        <span class="actions">действия</span>
                        <div class="action-list">
                           <span class="subarrowed">действия</span>
                           <div id="add_fiak_action" class="action '.($attestaciya ? 'hidden' : '').'"><span class="slink" onclick="add_fiak('.$kursModel['id'].')">Добавить</span></div>
                        </div>
                    </div>' : '').
                '</td>
                <td class="data">Итоговая аттестация</td>
                <td></td>
            </tr>
            '.($attestaciya ? KursGlobals::get_fiak_row($attestaciya,$status) : '').'
            <tr class="section_footer section_footer_razdel" id="section_footer_attestaciya"><td colspan="3"></td></tr>
       </tbody>
       </table>';

//var_dump($kims);
if ($kims) {
    echo '<h4>Контрольно-измерительные материалы</h4>';
    foreach ($kims as $item) {
        if ($kursModel['tip'] == 'pk' or ($item['type']==2))
            echo KursGlobals::get_kim_list_item($item);
    }
}

echo '<input type="hidden" id="kurs_type" value="'.$kursModel['tip'].'">';

if ($kursModel['tip'] == 'pk') echo $form->field($kursModel, 'spisok_literatury')->textarea(['class'=>'kurs_field form-control']);

//echo '<p><label for="status_programmy">Подписать&nbsp;&nbsp;</label>'.Html::checkbox('status_programmy',($kursModel['status_programmy']=='redaktiruetsya' or !$kursModel['status_programmy']) ? false : true,['id'=>'status_programmy','onchange'=>'change_podpis('.$kursModel['id'].')']).'  <a class="btn btn-primary" target="blank" href="/pdf/kurs?id='.$kursModel['id'].'">Печать</a></p>';

echo  '<a class="btn btn-primary" target="blank" href="/pdf/kurs?id='.$kursModel['id'].'">Печать</a></p>';

echo '<p>'.Html::button('Сохранить', ['id'=>'smb_btn','class' => 'btn btn-primary','clicked'=>'false']).'</p>';
ActiveForm::end();

echo '
<div class="podrazdel-form hidden" id="add_podrazdel_form">
    <div class="form-group">
        <label for="podrazdel_name">'.($kursModel['tip']=='pk' ? 'Наименование блока тем' : 'Наименование дисциплины').'</label> <input class="form-control" type="text" value="" id="podrazdel_name">
        <input type="hidden" id="razdel" value="">
     </div>
     <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
        <label for="rukovoditel_podrazdela">Руководитель</label>
        '.Html::dropDownList('rukovoditel_podrazdela',null,$sotrudniki,['id'=>'rukovoditel_podrazdela','class'=>'form-control']).'
     </div>
     <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
        <label for="podrazdel_lk">Количество лекционных часов</label>
        <input type="text" id="podrazdel_lk" value="" class="form-control">
     </div>
     <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
        <label for="podrazdel_pr">Количество практических часов</label>
        <input type="text" id="podrazdel_pr" value="" class="form-control">
     </div>
     <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
        <label for="podrazdel_srs">Количество часов СРС</label>
        <input type="text" id="podrazdel_srs" value="" class="form-control">
     </div>
     <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
            <label for="podrazdel_fk">Форма контроля</label>
            '.Html::dropDownList('podrazdel_fk',null,$kf_temi,['id'=>'podrazdel_fk','class'=>'form-control']).'
     </div>

     <div class="'.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
         <div class="inline-block vtop">
             <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                    <label for="podrazdel_fk_chasy">Часы контроля</label>
                    <input type="number" id="podrazdel_fk_chasy" value="" class="form-control">
             </div>
         </div>
         <div class="inline-block vtop" style="width:1.5em"></div>
         <div class="inline-block vtop">
             <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                    <label for="podrazdel_nedelya_nachalo">Первая неделя</label>
                    '.Html::dropDownList('podrazdel_nedelya_nachalo',null,$weeks,['id'=>'podrazdel_nedelya_nachalo','class'=>'form-control']).'
             </div>
         </div>
         <div class="inline-block vtop" style="width:1.5em"></div>
         <div class="inline-block vtop">
             <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                    <label for="podrazdel_nedelya_konec">Последняя неделя</label>
                    '.Html::dropDownList('podrazdel_nedelya_konec',null,$weeks,['id'=>'podrazdel_nedelya_konec','class'=>'form-control']).'
             </div>
         </div>
     </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_podrazdel()">Сохранить</button> <span onclick="hide_form(\'add_podrazdel_form\')" class="slink">отмена</span>
</div>';

echo '<div class="podrazdel-form top_arrow hidden" id="edit_podrazdel_form">
            <div class="form-group">
                <label for="edit_podrazdel_name">'.($kursModel['tip']=='pk' ? 'Наименование блока тем' : 'Наименование дисциплины').'</label>
                <input type="text" value="" id="edit_podrazdel_name" class="form-control">
            </div>
            <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                <label for="edit_rukovoditel_podrazdela">Руководитель</label>
                '.Html::dropDownList('edit_rukovoditel_podrazdela',null,$sotrudniki,['id'=>'edit_rukovoditel_podrazdela','class'=>'form-control']).'
             </div>
             <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                <label for="edit_podrazdel_lk">Количество лекционных часов</label>
                <input type="text" id="edit_podrazdel_lk" value="" class="form-control">
             </div>
             <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                <label for="edit_podrazdel_pr">Количество практических часов</label>
                <input type="text" id="edit_podrazdel_pr" value="" class="form-control">
             </div>
             <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                <label for="edit_podrazdel_srs">Количество часов СРС</label>
                <input type="text" id="edit_podrazdel_srs" value="" class="form-control">
             </div>
             <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                    <label for="edit_podrazdel_fk">Форма контроля</label>
                    '.Html::dropDownList('edit_podrazdel_fk',null,$kf_temi,['id'=>'edit_podrazdel_fk','class'=>'form-control']).'
             </div>
             <div class="'.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                 <div class="inline-block vtop">
                     <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                        <label for="edit_podrazdel_fk_chasy">Часы контроля</label>
                        <input type="number" id="edit_podrazdel_fk_chasy" value="" class="form-control">
                     </div>
                 </div>
                 <div class="inline-block vtop" style="width:1.5em"></div>
                 <div class="inline-block vtop">
                     <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                            <label for="edit_podrazdel_nedelya_nachalo">Первая неделя</label>
                            '.Html::dropDownList('edit_podrazdel_nedelya_nachalo',null,$weeks,['id'=>'edit_podrazdel_nedelya_nachalo','class'=>'form-control']).'
                     </div>
                 </div>
                 <div class="inline-block vtop" style="width:1.5em"></div>
                 <div class="inline-block vtop">
                     <div class="form-group '.($kursModel['tip']=='pk' ? 'hidden' : '' ).'">
                            <label for="edit_podrazdel_nedelya_konec">Последняя неделя</label>
                            '.Html::dropDownList('edit_podrazdel_nedelya_konec',null,$weeks,['id'=>'edit_podrazdel_nedelya_konec','class'=>'form-control']).'
                     </div>
                 </div>
             </div>
            <span class="btn btn-default btn-primary" onclick="save_edit_podrazdel()">Сохранить</span> <span class="slink" onclick="hide_form(\'edit_podrazdel_form\')">отмена</span>
            <input type="hidden" value="" id="edit_podrazdel_id">
        </div>';

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
     <div class="form-group '.($kursModel['tip']!='pk' ? 'hidden' : '').'">
         <label for="soderzhanie">Аннотация</label>
         <textarea class="form-control" id="soderzhanie"></textarea>
     </div>
     <div>
        <span class="inline-block vtop">
         <div class="form-group">
            <label for="theme_chasy">Кол-во часов</label>
             <input class="form-control" size="3"  style="width:4.5em" min="2" '.($kursModel['tip']=='pk' ? 'max="4"' : '').' type="number" step="2" id="theme_chasy" value=""/>
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
     <button class="btn btn-default btn-primary" type="button" onclick="save_theme()">Сохранить</button> <span onclick="hide_form(\'add_theme_form\')" class="slink">отмена</span>
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
     <div class="form-group '.($kursModel['tip']!='pk' ? 'hidden' : '').'">
         <label for="soderzhanie_edit">Аннотация</label>
         <textarea class="form-control" id="soderzhanie_edit"></textarea>
     </div>
     <div>
         <span class="inline-block vtop">
         <div class="form-group">
            <label for="theme_edit_chasy">Кол-во часов</label>
            <input class="form-control" size="3"  style="width:4.5em" min="2" '.($kursModel['tip']=='pk' ? 'max="4"' : '').' type="number" step="2" id="theme_edit_chasy" value=""/>
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
     <button class="btn btn-default btn-primary" type="button" onclick="save_edit_theme()">Сохранить</button> <span onclick="hide_form(\'edit_theme_form\')" class="slink">отмена</span>
</div>';

echo '
<div class=" umk-form podrazdel-form hidden" id="add_umk_form">
    <div class="form-group">
        <label for="umk_opisanie">Описание</label>
        <textarea class="form-control" id="umk_opisanie"></textarea>
        <input type="hidden" id="umk_theme_id" value="">
    </div>
    <div class="form-group">
        '. \app\widgets\UmkTypeWidget::widget(['params'=>['id'=>'umk']]).'
    </div>

     <button class="btn btn-default btn-primary" type="button" onclick="save_umk()">Сохранить</button> <span onclick="hide_form(\'add_umk_form\')" class="slink">отмена</span>
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
     <button class="btn btn-default btn-primary" type="button" onclick="save_edit_umk()">Сохранить</button> <span onclick="hide_form(\'edit_umk_form\')" class="slink">отмена</span>
</div>';

echo '
<div class=" cc-form podrazdel-form hidden" id="add_cc_form">
    <div class="form-group">
        <label for="forma_kontrolya_temi">Форма контроля</label>
        '.Html::dropDownList('forma_kontrolya_temi',null,$kf_temi,['id'=>'forma_kontrolya_temi','class'=>'form-control']).'
        <input type="hidden" value="" id="theme_kf_id">
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_kf()">Сохранить</button> <span onclick="hide_form(\'add_cc_form\')" class="slink">отмена</span>
</div>';

echo '
<div class="podrazdel-form top_arrow hidden" id="add_cc_edit_form">
   <div class="form-group">
        <label for="forma_kontrolya_temi_edit">Форма контроля</label>
        '.Html::dropDownList('forma_kontrolya_temi_edit',null,$kf_temi,['id'=>'forma_kontrolya_temi_edit','class'=>'form-control']).'
        <input type="hidden" value="" id="theme_kf_edit_id">
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_edit_kf()">Сохранить</button> <span onclick="hide_form(\'add_cc_edit_form\')" class="slink">отмена</span>
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
     <button class="btn btn-default btn-primary" type="button" onclick="save_kim()">Сохранить</button> <span onclick="hide_form(\'add_kim_form\')" class="slink">отмена</span>
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
     <button class="btn btn-default btn-primary" type="button" onclick="save_kim_edit()">Сохранить</button> <span onclick="hide_form(\'edit_kim_form\')" class="slink">отмена</span>
</div>';


echo '
<div class=" fiak-form podrazdel-form hidden" id="add_fiak_form">
    <div class="form-group">
        <span class="inline-block vtop">
            <label for="fiak_id">Тип аттестации</label>
           '.Html::dropDownList('fiak_id',null,$fiak,['id'=>'fiak_id','class'=>'form-control']).'
           <input type="hidden" id="fiak_kurs_id" value="'.$kursModel['id'].'">
       </span>
       <span class="inline-block vtop">
            <label for="ia_chasy">Часы</label>
            <input type="number" step="2" style="width:4.5em" class="form-control" value="" id="ia_chasy">
        </span>
    </div>
    <div class="form-group">
        <label for="prepods_fiak">Преподаватели</label><br>
        '. \app\widgets\MultipleSelect::widget(['params'=>['id'=>'prepods_fiak','data'=>$sotrudniki]]).'
    </div>
    <div class="form-group">
        <label for="fiak_week">Неделя</label>
        '.Html::dropDownList('fiak_week',null,$weeks,['id'=>'fiak_week','class'=>'form-control']).'
    </div>
    <div class="form-group">
        <label for="fiak_opisanie">Описание</label>
        <textarea class="form-control" id="fiak_opisanie" style="width:25em"></textarea>
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_fiak()">Сохранить</button> <span onclick="hide_form(\'add_fiak_form\')" class="slink">отмена</span>
</div>';

echo '
<div class="podrazdel-form top_arrow hidden" id="edit_fiak_form">
    <div class="form-group">
        <span class="inline-block vtop">
            <label for="fiak_edit_id">Тип аттестации</label>
           '.Html::dropDownList('fiak_edit_id',null,$fiak,['id'=>'fiak_edit_id','class'=>'form-control']).'
           <input type="hidden" id="fiak_edit_kurs_id" value="'.$kursModel['id'].'">
       </span>
       <span class="inline-block vtop">
            <label for="ia_edit_chasy">Часы</label>
            <input type="number" step="2" style="width:4.5em" class="form-control" value="" id="ia_edit_chasy">
        </span>
    </div>
    <div class="form-group">
        <label for="prepods_fiak_edit">Преподаватели</label><br>
        '. \app\widgets\MultipleSelect::widget(['params'=>['id'=>'prepods_fiak_edit','data'=>$sotrudniki]]).'
    </div>
    <div class="form-group">
        <label for="edit_fiak_week">Неделя</label>
        '.Html::dropDownList('edit_fiak_week',null,$weeks,['id'=>'edit_fiak_week','class'=>'form-control']).'
    </div>
    <div class="form-group">
        <label for="fiak_edit_opisanie">Описание</label>
        <textarea class="form-control" id="fiak_edit_opisanie" style="width:25em"></textarea>
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_edit_fiak()">Сохранить</button> <span onclick="hide_form(\'edit_fiak_form\')" class="slink">отмена</span>
</div>';

echo '
<div class=" dr-form podrazdel-form hidden" id="add_theme_dr_form">
    <div class="form-group">
       <label for="theme_dr_name">Название</label>
       <input type="text" id="theme_dr_name" value="" class="form-control"/>
       <input type="hidden" id="theme_dr_kurs_id" value="'.$kursModel['id'].'">
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_theme_dr()">Сохранить</button> <span onclick="hide_form(\'add_theme_dr_form\')" class="slink">отмена</span>
</div>';

echo '
<div class="podrazdel-form top_arrow hidden" id="edit_theme_dr_form">
    <div class="form-group">
       <label for="edit_theme_dr_name">Название</label>
       <input type="text" id="edit_theme_dr_name" value="" class="form-control"/>
       <input type="hidden" id="edit_theme_dr_kurs_id" value="'.$kursModel['id'].'">
       <input type="hidden" id="edit_theme_dr_id" value="">
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="edit_theme_dr_save()">Сохранить</button> <span onclick="hide_form(\'edit_theme_dr_form\')" class="slink">отмена</span>
</div>';

echo '
<div class=" podrazdel-kf-form podrazdel-form hidden" id="add_podrazdel_kf_form">
    <div class="form-group">
        <span class="vtop inline-block">
            <label for="forma_kontrolya_podrazdel">Форма контроля</label>
            '.Html::dropDownList('forma_kontrolya_podrazdel',null,$kf_temi,['id'=>'forma_kontrolya_podrazdel','class'=>'form-control']).'
            <input type="hidden" value="" id="podrazdel_kf_id">
        </span>
        <span class="vtop inline-block">
            <label for="fk_podrazdel_chasy">Часы</label>
            <input type="number" step="2" style="width:4.5em" class="form-control" value="" id="fk_podrazdel_chasy">
        </span>
    </div>
    <div class="form-group">
        <label for="prepods_podrazdel_kf">Преподаватели</label><br>
        '. \app\widgets\MultipleSelect::widget(['params'=>['id'=>'prepods_podrazdel_kf','data'=>$sotrudniki]]).'
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_podrazdel_kf()">Сохранить</button> <span onclick="hide_form(\'add_podrazdel_kf_form\')" class="slink">отмена</span>
</div>';

echo '
<div class="podrazdel-form top_arrow podrazdel-kf-form hidden" id="edit_podrazdel_kf_form">
    <div class="form-group">
        <span class="vtop inline-block">
            <label for="edit_forma_kontrolya_podrazdel">Форма контроля</label>
            '.Html::dropDownList('edit_forma_kontrolya_podrazdel',null,$kf_temi,['id'=>'edit_forma_kontrolya_podrazdel','class'=>'form-control']).'
            <input type="hidden" value="" id="edit_podrazdel_kf_id">
        </span>
        <span class="vtop inline-block">
            <label for="edit_fk_podrazdel_chasy">Часы</label>
            <input type="number" step="2" style="width:4.5em" class="form-control" value="" id="edit_fk_podrazdel_chasy">
        </span>
    </div>
    <div class="form-group">
        <label for="prepods_podrazdel_kf_edit">Преподаватели</label><br>
        '. \app\widgets\MultipleSelect::widget(['params'=>['id'=>'prepods_podrazdel_kf_edit','data'=>$sotrudniki]]).'
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_edit_podrazdel_kf()">Сохранить</button> <span onclick="hide_form(\'edit_podrazdel_kf_form\')" class="slink">отмена</span>
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
     <button class="btn btn-default btn-primary" type="button" onclick="save_podrazdel_kim()">Сохранить</button> <span onclick="hide_form(\'add_podrazdel_kim_form\')" class="slink">отмена</span>
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

     <button class="btn btn-default btn-primary" type="button" onclick="save_podrazdel_umk()">Сохранить</button> <span onclick="hide_form(\'add_podrazdel_umk_form\')" class="slink">отмена</span>
</div>';

//КИМ курса
echo '
<div class=" cc-form podrazdel-form hidden" id="add_kurs_kim_form">
    <div class="form-group">
        <label for="kurs_kim_opisanie">Описание</label>
        <textarea class="form-control" id="kurs_kim_opisanie"></textarea>
        <input type="hidden" id="kim_kurs_id" value="">
    </div>
    <div class="form-group">
        '.KimTypeWidget::widget(['params'=>['id'=>'kurs_kim']]).'
    </div>
     <button class="btn btn-default btn-primary" type="button" onclick="save_kurs_kim()">Сохранить</button> <span onclick="hide_form(\'add_kurs_kim_form\')" class="slink">отмена</span>
</div>';

/////////////////

echo '<div class="loader hidden"></div>';