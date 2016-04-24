<?php

namespace app\globals;

use app\entities\Kurs;
//use yii\globals;
use app\enums\StatusProgrammyKursa;
use \Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class KursGlobals {

    public static function get_razdel_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full=true){
        $html = '<tr id="razdel'.$item['razdel_id'].'" class="section atr numbered  razdel-row">
                <td class="action-td">
                    '.($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="actions-control">
                            <span class="actions">действия</span>
                            <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div class="action"><span onclick="add_podrazdel('.$item['razdel_id'].')" class="slink">Добавить '.($item['tip_kursa'] == 'pk' ? 'блок тем' : 'дисциплину').'</span></div>
                               <div class="action"><span onclick="edit_razdel('.$item['razdel_id'].')" class="slink">Редактировать раздел</span></div>
                               <div class="action"><span onclick="delete_razdel('.$item['razdel_id'].')" class="slink">Удалить раздел</span></div>
                            </div>
                        </div>' : '').
                '</td>
                <td class="data">'.$item['razdel_nazvanie'].'
                <input type="hidden" value="'.$item['rk_nazvanie_id'].'" id="razdel_nazvanie_id'.$item['razdel_id'].'">
                <input type="hidden" value="'.$item['razdel_tip'].'" id="razdel_tip'.$item['razdel_id'].'">
                </td>
                <td></td>
            </tr>';
        if ($is_full){
            if (isset($item['podrazdels']) and $item['podrazdels']){
                foreach($item['podrazdels'] as $k=>$v){
                    $html.=KursGlobals::get_podrazdel_row($v,$status);
                }
                //$html .= get_podrazdel_row($item['podrazdels']);
            }
            $html .= '<tr class="section_footer section_footer_razdel" id="section_footer_'.$item['razdel_id'].'"><td colspan="3"></td></tr>';
        }
        return $html;
    }

    public static function get_podrazdel_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full=true){
        $chasy_sum = $item['podrazdel_lk']+$item['podrazdel_pr']+$item['podrazdel_srs'];
        $html =  '<tr id="podrazdel'.$item['id'].'" class="podrazdel'.$item['razdel_id'].' atr podrazdel-row numbered">
                    <td class="action-td">
                    '.($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               '.($item['tip_kursa']=='pk' ? '<div class="action"><span class="slink"  onclick="add_theme('.$item['id'].')">Добавить тему</span></div>' : '').'
                               '.($item['tip_kursa']=='pk' ? '<div class="action"><span class="slink"  onclick="add_podrazdel_umk('.$item['id'].')">Добавить УМК</span></div>': '').'
                               '.($item['tip_kursa']=='pk' ? '<div id="add_podrazdel_kf_action'.$item['id'].'" class="action '.($item['kf_podrazdel_id'] ? 'hidden' : '').'"><span onclick="add_podrazdel_fk('.$item['id'].')" class="slink">Добавить форму контроля</span></div>' : '').'
                               <div class="action"><span onclick="edit_podrazdel('.$item['id'].')" class="slink">Редактировать '.($item['tip_kursa'] == 'pk' ? 'блок тем' : 'дисциплину').'</span></div>
                               <div class="action"><span onclick="delete_podrazdel('.$item['id'].')" class="slink">Удалить '.($item['tip_kursa'] == 'pk' ? 'блок тем' : 'дисциплину').'</span></div>
                            </div>
                        </div>' : '').
                    '</td>
                    <td class="data">
                        <div class="podrazdel">
                            <span class="num"></span>
                            <span id="podrazdel_name'.$item['id'].'">'.$item['nazvanie'].'</span>
                            '.($item['rukovoditel_podrazdela_id'] ? ', '.$item['rukovoditel_podrazdela_fio'] : '') .'
                            '.($item['podrazdel_lk'] ? ', ЛК: <span id="podrazdel_lk'.$item['id'].'">'.$item['podrazdel_lk'].'</span>' : '') .'
                            '.($item['podrazdel_pr'] ? ', ПР: <span id="podrazdel_pr'.$item['id'].'">'.$item['podrazdel_pr'].'</span>' : '') .'
                            '.($item['podrazdel_pr'] ? ', СРС: <span id="podrazdel_srs'.$item['id'].'">'.$item['podrazdel_srs'].'</span>' : '') .'
                            '.($chasy_sum ? ', Всего: '.$chasy_sum : '') .'
                            '.(($item['tip_kursa']!='pk' and $item['kf_podrazdela_name']) ? ', форма контроля: '.$item['kf_podrazdela_name'] : '').'
                            '.(($item['tip_kursa']!='pk' and $item['podrazdel_nedelya_nachalo']) ? ', недели с '.$item['podrazdel_nedelya_nachalo'].' по '.$item['podrazdel_nedelya_konec'] : '').'
                            <input id="rp'.$item['id'].'" type="hidden" value="'.$item['rukovoditel_podrazdela_id'].'">
                        </div>
                        <input type="hidden" value="'.$item['nomer'].'" class="podrazdel_nomer" id="podrazdel_nomer'.$item['id'].'"/>
                        <input type="hidden" value="'.$item['id'].'" class="podrazdel_id">
                        <input type="hidden" value="'.$item['kf_podrazdel_id'].'" id="kf_podrazdel'.$item['id'].'">
                        <input type="hidden" value="'.$item['podrazdel_nedelya_nachalo'].'" id="nedelya_nachalo'.$item['id'].'">
                        <input type="hidden" value="'.$item['podrazdel_nedelya_konec'].'" id="nedelya_konec'.$item['id'].'">
                        <input type="hidden" value="'.$item['chasy_kf_podrazdela'].'" id="chasy_kf_podrazdela'.$item['id'].'">
                    </td>
                    <td>
                    '.($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="movers">
                            <span onclick="podrazdel_up('.$item['id'].','.$item['razdel_id'].')" class="inline-block mover_arrow" title="Переместить подраздел вверх">⬆</span><br>
                            <span onclick="podrazdel_down('.$item['id'].','.$item['razdel_id'].')" class="inline-block mover_arrow"  title="Переместить подраздел вниз">⬇</span>
                        </div>' : '').
                    '</td>
                </tr>';
        if ($is_full) {
            if (isset($item['themes'])) {
                foreach ($item['themes'] as $tk => $tv) {
                    $html .= KursGlobals::get_theme_row($tv,$status);
                }
            }
        }
        $html .= '<tr class="section_footer section_footer_podrazdel" id="section_footer_podrazdel' . $item['id'] . '"><td colspan="3"></td></tr>';
        if ($is_full) {
            if (isset($item['kf_podrazdel_id']) and $item['tip_kursa']=='pk') {
                $html .= KursGlobals::get_kf_podrazdela_row($item,$status);
            }
            if (isset($item['podrazdel_kims']) and $item['tip_kursa']!='pk'){
                foreach ($item['podrazdel_kims'] as $k => $v) {
                    $html .= KursGlobals::get_kim_row($v,$status);
                }
            }
            if (isset($item['podrazdel_umks'])){
                foreach($item['podrazdel_umks'] as $key=>$value){
                    $html.= KursGlobals::get_umk_row($value,$status);
                }
            }
        }
        $html .= '<tr class="section_footer section_footer_podrazdel" id="section_footer_podrazdel_kf' . $item['id'] . '"><td colspan="3"></td></tr>';
        return $html;
    }


    public static function get_theme_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full=true){
        $html= '<tr id="theme'.$item['theme_id'].'" class="theme'.$item['id'].' atr theme-row numbered">
                    <td class="action-td">';
        if ($item['tip_kursa']=='pk' and $status == StatusProgrammyKursa::REDAKTIRUETSYA)
                        $html.='<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div id="add_kf_block'.$item['theme_id'].'" class="action '.(isset($item['theme_forma_kontrolya_name']) ? 'hidden' : '').' "><span class="slink"  onclick="add_theme_control_form('.$item['theme_id'].')">Добавить форму контроля</span></div>
                               '.($item['tip_kursa']=='pk' ? '<div class="action"><span class="slink" onclick="add_umk('.$item['theme_id'].')">Добавить УМК</span></div>' : '').'
                               <div class="action"><span onclick="edit_them('.$item['theme_id'].')" class="slink">Редактировать тему</span></div>
                               <div class="action"><span onclick="delete_theme('.$item['theme_id'].')" class="slink">Удалить тему</span></div>
                            </div>
                        </div>';
        $html.= '</td>
                    <td class="data">
                        <div class="theme">
                                   <b><span class="num"></span>
                                   <span id="theme_nazvanie'.$item['theme_id'].'">'.$item['theme_nazvanie'].'</span>, '.ApiGlobals::first_letter_up($item['tip_rabot_name']).', '.$item['fio_prepodavatelya'].'.
                                   (<span id="theme_chasy'.$item['theme_id'].'">'.$item['theme_chasy'].'</span> ч., <span id="theme_week'.$item['theme_id'].'">'.$item['theme_nedelya'].'</span> неделя)</b>
                                   <br>
                                   <span id="soderzhanie'.$item['theme_id'].'"><i>'.($item['theme_soderzhanie'] ? ''.$item['theme_soderzhanie'] : '').'</i></span>
                                   <input type="hidden" id="prepodavatel'.$item['theme_id'].'" value="'.$item['theme_prepodavatel'].'">
                                   <input type="hidden" id="vid_rabot'.$item['theme_id'].'" value="'.$item['theme_tip_raboty'].'">
                                   <input type="hidden" id="theme_nomer'.$item['theme_id'].'" value="'.$item['theme_nomer'].'" class="theme_nomers">
                                   <input type="hidden" id="theme_id'.$item['theme_id'].'" value="'.$item['theme_id'].'" class="theme_id">
                        </div>
                    </td>
                    <td>
                        '.(($item['tip_kursa']=='pk' and $status == StatusProgrammyKursa::REDAKTIRUETSYA) ? '<div class="movers">
                            <span onclick="theme_up('.$item['theme_id'].','.$item['id'].')" class="inline-block mover_arrow" title="Переместить тему вверх">⬆</span><br>
                            <span onclick="theme_down('.$item['theme_id'].','.$item['id'].')" class="inline-block mover_arrow"  title="Переместить тему вниз">⬇</span>
                        </div>' : '').'
                    </td>
                </tr>';
        if ($is_full) {
            if (isset($item['theme_forma_kontrolya_name'])){
                $html.=KursGlobals::get_kf_row($item,$status);
            }
            if (isset($item['umks'])) {
                foreach ($item['umks'] as $k => $v) {
                    $html .= KursGlobals::get_umk_row($v,$status);
                }

            }
            $html .= '<tr class="section_footer section_footer_theme" id="section_footer_theme' . $item['theme_id'] . '"><td colspan="3"></td></tr>';
        }
        return $html;
    }

    public static function get_umk_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full=true){
        $html = '<tr id="umk'.$item['umk_id'].'" class="atr umk-row">
                    <td class="action-td">';
        if ($item['tip_kursa']=='pk' and $status == StatusProgrammyKursa::REDAKTIRUETSYA)
            $html.='<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div class="action"><span class="slink"  onclick="edit_umk('.$item['umk_id'].')">Редактировать УМК</span></div>
                               <div class="action"><span class="slink" onclick="delete_umk('.$item['umk_id'].','.$item['tip'].')">Удалить УМК</span></div>
                            </div>
                        </div>';
        $html.='</td>
                    <td class="data">
                        <div class="umk">
                        УМК -
                            '.($item['umk_fajl'] ? '<a id="umk_file_name'.$item['umk_id'].'" href="'.Url::to(ApiGlobals::get_user_dir_url().$item['umk_file_disk_name']).'">'.$item['umk_file_show_name'].'</a><input type="hidden" value="'.$item['umk_fajl'].'" id="umk_file_id'.$item['umk_id'].'">' : '').'
                            '.($item['umk_uri'] ? '<a id="url'.$item['umk_id'].'" href="'.$item['umk_uri'].'">'.$item['umk_uri'].'</a>' : '').'
                            '.($item['umk_opisanie'] ? '<br><span id="umk_opisanie'.$item['umk_id'].'">'.$item['umk_opisanie'].'</span>' : '').'
                        </div>
                        <input type="hidden" id="tip'.$item['umk_id'].'" value="'.$item['tip'].'">
                    </td>
                    <td></td>
                </tr>';
        if ($is_full)
            $html.= '<tr class="section_footer" id="section_footer_umk'.$item['umk_id'].'"><td colspan="3"></td></tr>';
        return $html;
    }
    
    public static function get_kf_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full=true){
        $html = '<tr id="kf'.$item['theme_id'].'" class="atr kf-row">
                    <td class="action-td">';
        if ($item['tip_kursa']=='pk' and $status == StatusProgrammyKursa::REDAKTIRUETSYA)
        $html.='<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div class="action"><span class="slink"  onclick="add_kim('.$item['theme_id'].')">Добавить КИМ</span></div>
                               <div class="action"><span class="slink"  onclick="edit_kf('.$item['theme_id'].')">Редактировать</span></div>
                               <div class="action"><span class="slink" onclick="delete_kf('.$item['theme_id'].')">Удалить</span></div>
                            </div>
                        </div>
                    </td>';
        $html.='<td class="data">
                        <div class="umk">
                            Форма контроля - '.$item['theme_forma_kontrolya_name'].'
                            <input type="hidden" id="kf_id'.$item['theme_id'].'" value="'.$item['theme_forma_kontrolya'].'">
                        </div>
                    </td>
                    <td></td>
                </tr>';
        if ($is_full){
            if (isset($item['kims'])){
                foreach ($item['kims'] as $k => $v) {
                    $html .= KursGlobals::get_kim_row($v);
                }
            }
            $html.= '<tr class="section_footer" id="section_footer_kf'.$item['theme_id'].'"><td colspan="3"></td></tr>';
        }
        return $html;
    }

    public static function get_kim_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_attestaciya = false){
        $html = '<tr id="kim'.$item['kim_id'].'" class="atr kim-row">
                    <td class="action-td">';
        if (($item['tip_kursa']=='pk' or $is_attestaciya) and $status == StatusProgrammyKursa::REDAKTIRUETSYA)
        $html.='<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div class="action"><span class="slink"  onclick="edit_kim('.$item['kim_id'].')">Редактировать КИМ</span></div>
                               <div class="action"><span class="slink" onclick="delete_kim('.$item['kim_id'].','.$item['tip'].')">Удалить КИМ</span></div>
                            </div>
                        </div>';
        $html.='</td>
                    <td class="data">
                        <div class="kim">
                        КИМ -
                            '.($item['kim_fajl'] ? '<a id="kim_file_name'.$item['kim_id'].'" href="'.Url::to(ApiGlobals::get_user_dir_url().$item['kim_file_disk_name']).'">'.$item['kim_file_show_name'].'</a><input type="hidden" value="'.$item['kim_fajl'].'" id="kim_file_id'.$item['kim_id'].'"><br>' : '').'
                            '.($item['kim_uri'] ? '<a id="kim_url'.$item['kim_id'].'" href="'.$item['kim_uri'].'">'.$item['kim_uri'].'</a><br>' : '').'
                            '.($item['kim_text'] ? '<div class="kim-text" id="kim_text'.$item['kim_id'].'">'.$item['kim_text'].'</div>' : '').'
                            '.($item['kim_opisanie'] ? '<span id="kim_opisanie'.$item['kim_id'].'">'.$item['kim_opisanie'].'</span>' : '').'
                        </div>
                        <input type="hidden" id="kim_tip'.$item['kim_id'].'" value="'.$item['tip'].'">
                    </td>
                    <td></td>
                </tr>';
        return $html;
    }

    public static function get_fiak_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full = true){
        $html = '<tr id="fiak'.$item['kurs_id'].'" class="atr fiak-row">
                    <td class="action-td">
                    '. ($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               '.($item['tip_kursa'] != 'pk' && $item['fiak_id'] == 4 ? '<div class="action"><span class="slink"  onclick="add_them_dr('.$item['kurs_id'].')">Добавить тему итоговой работы</span></div>' : '<div class="action"><span class="slink"  onclick="add_kurs_kim('.$item['kurs_id'].')">Добавить КИМ</span></div>').'
                               <div class="action"><span class="slink"  onclick="edit_fiak('.$item['kurs_id'].')">Редактировать</span></div>
                               <div class="action"><span class="slink" onclick="delete_fiak('.$item['kurs_id'].')">Удалить</span></div>
                            </div>
                        </div>' : '').
                    '</td>
                    <td class="data">
                        <div class="fiak">
                            <span>'.ApiGlobals::first_letter_up($item['forma_attestacii']).'</span>, <span id="fiak_chasy'.$item['kurs_id'].'">'.$item['chasy'].'</span> ч., <span id="fiak_week'.$item['kurs_id'].'">'.$item['nedelya'].'</span> неделя
                            <br>'.
                            (isset($item['opisanie']) ? '<span id="fiak_opisanie'.$item['kurs_id'].'">'.$item['opisanie'].'</span><br>' : '').
                            (isset($item['kontrols']) ? '<span>'.implode(',', array_map(function ($v, $k) { return $v; }, $item['kontrols'], array_keys($item['kontrols']))).'</span>' : '').
                            (isset($item['kontrols']) ? '<span id="kontrols_ids'.$item['kurs_id'].'" class="hidden">'.implode(',', array_map(function ($v, $k) { return $k; }, $item['kontrols'], array_keys($item['kontrols']))).'</span>' : '')
                            .'<input type="hidden" id="fiak_id'.$item['kurs_id'].'" value="'.$item['fiak_id'].'">
                        </div>
                    </td>
                    <td></td>
                </tr>';
        if ($is_full) {
            if (isset($item['themes_dr'])){
                foreach ($item['themes_dr'] as $k=>$v) {
                    $html .= KursGlobals::get_theme_dr_row($v,$status);
                }
            }
            if (isset($item['kims'])){
                foreach ($item['kims'] as $k=>$v) {
                    $html .= KursGlobals::get_kim_row($v,$status,true);
                }
            }
            $html .= '<tr class="section_footer" id="section_footer_fiak' . $item['kurs_id'] . '"><td colspan="3"></td></tr>';
        }
        return $html;
    }

    public static function get_theme_dr_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA){
        $html = '<tr id="theme_dr'.$item['id'].'" class="atr theme-dr-row">
                    <td class="action-td">
                    '. ($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div class="action"><span class="slink"  onclick="edit_theme_dr('.$item['id'].')">Редактировать</span></div>
                               <div class="action"><span class="slink" onclick="delete_theme_dr('.$item['id'].')">Удалить</span></div>
                            </div>
                        </div>' : '').
                    '</td>
                    <td class="data">
                        <div class="theme_dr">
                            Тема итоговой работы - <span id="theme_dr_name'.$item['id'].'">'.$item['theme_dr_name'].'</span>
                        </div>
                    </td>
                    <td></td>
                </tr>';
        return $html;
    }

    public static function get_kf_podrazdela_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full=true){
        $html = '<tr id="podrazdel_kf'.$item['id'].'" class="atr kf-row">
                    <td class="action-td">
                    '.($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div class="action"><span class="slink"  onclick="add_podrazdel_kf_kim('.$item['id'].')">Добавить КИМ</span></div>
                               <div class="action"><span class="slink"  onclick="edit_podrazdel_kf('.$item['id'].')">Редактировать</span></div>
                               <div class="action"><span class="slink" onclick="delete_podrazdel_kf('.$item['id'].')">Удалить</span></div>
                            </div>
                        </div>' : '').
                    '</td>
                    <td class="data">
                        <div class="fk-podrazdel">
                            Форма контроля '.($item['tip_kursa']=='pk' ? 'блока тем' : 'дисциплины').' - '.$item['kf_podrazdela_name'].' (<span id="chasy_kf_podrazdel'.$item['id'].'">'.$item['chasy_kf_podrazdela'].'</span> ч.)
                            '.(isset($item['kontrol_fiz_lica']) ? '<br>'.implode(',', array_map(function ($v, $k) { return $v; }, $item['kontrol_fiz_lica'], array_keys($item['kontrol_fiz_lica']))) : '').
                            (isset($item['kontrol_fiz_lica']) ? '<span id="kontrols_pk_ids'.$item['id'].'" class="hidden">'.implode(',', array_map(function ($v, $k) { return $k; }, $item['kontrol_fiz_lica'], array_keys($item['kontrol_fiz_lica']))).'</span>' : '').'
                            <input type="hidden" id="podrazdel_kf_id'.$item['id'].'" value="'.$item['kf_podrazdel_id'].'">
                        </div>
                    </td>
                    <td></td>
                </tr>';
        if ($is_full){
            if (isset($item['podrazdel_kims'])){
                foreach ($item['podrazdel_kims'] as $k => $v) {
                    $html .= KursGlobals::get_kim_row($v);
                }
            }
            $html.= '<tr class="section_footer" id="section_footer_kf_podrazdela'.$item['id'].'"><td colspan="3"></td></tr>';
        }
        return $html;
    }

    public static function get_razdel_by_id($razdel_id){
        $sql = 'SELECT k.tip as tip_kursa, rk.id razdel_id,nk.nazvanie razdel_nazvanie,
                       rk.nazvanie as rk_nazvanie_id,rk.tip as razdel_tip
                FROM kurs as k
                INNER JOIN razdel_kursa as rk on k.id = rk.kurs
                INNER JOIN nazvanie_dlya_razdela_kursa as nk on rk.nazvanie = nk.id
                where rk.id = :razdel_id';
        $razdel = [];
        $res = Yii::$app->db->createCommand($sql)->bindValue(':razdel_id',$razdel_id)->queryOne();
        if ($res) $razdel = $res;
        return $razdel;
    }


    public static function get_vidy_rabot(){
        $sql = 'select * from rabota_po_teme';
        $vidy_rabot = array();
        if ($res = Yii::$app->db->createCommand($sql)->queryAll()){
            foreach ($res as $k=>$v) {
                $vidy_rabot[$v['id']]=$v['nazvanie'];
            }
        }
        return $vidy_rabot;
    }

    public static function get_sotrudniki(){
        $sql='SELECT fl.id, fl.familiya||\' \'||fl.imya||\' \'||fl.otchestvo as fio,sp.nazvanie as podrazdelenie
              FROM fiz_lico as fl
                INNER JOIN rabota_fiz_lica as rfl on fl.id=rfl.fiz_lico
                INNER JOIN dolzhnost_fiz_lica_na_rabote as dnr on rfl.id = dnr.rabota_fiz_lica
                INNER JOIN strukturnoe_podrazdelenie as sp on dnr.strukturnoe_podrazdelenie = sp.id
                INNER JOIN dolzhnost as d on dnr.dolzhnost = d.id
                where d.tip = \'profprep\'
              ORDER BY fl.familiya,fl.imya,fl.otchestvo';
        $sotrudniki = array();
        if ($res = Yii::$app->db->createCommand($sql)->queryAll()){
            foreach ($res as $k=>$v) {
                $sotrudniki[$v['id']] = $v['fio'].', '.$v['podrazdelenie'];
           }
        }
        return $sotrudniki;
    }

    public static function get_kontrolnie_formi_temi(){
        $sql = 'select * from forma_kontrolya_v_techenie_kursa';
        $forma_controlya = array();
        if ($res = Yii::$app->db->createCommand($sql)->queryAll()){
            foreach ($res as $k=>$v) {
                $forma_controlya[$v['id']] = $v['nazvanie'];
            }
        }
        return $forma_controlya;
    }

    public static function get_formi_itogovoi_attestacii(){
        $sql = 'SELECT * FROM forma_itogovoj_attestacii_kursa';
        $fiak = array();
        if ($res = Yii::$app->db->createCommand($sql)->queryAll()){
            foreach ($res as $k=>$v) {
                $fiak[$v['id']] = $v['nazvanie'];
            }
        }
        return $fiak;
    }

    public static function get_theme_by_id ($id){
        $sql = 'SELECT t.podrazdel as id, t.id as theme_id, t.chasy as theme_chasy,
                      t.nazvanie as theme_nazvanie,t.soderzhanie as theme_soderzhanie,
                      t.forma_kontrolya as theme_forma_kontrolya, t.nedelya as theme_nedelya,
                      case when t.prepodavatel_vakansiya then -1 else t.prepodavatel_fiz_lico end as theme_prepodavatel,
                      t.nomer as theme_nomer,
                      t.tip_raboty as theme_tip_raboty,r.nazvanie as tip_rabot_name,
                      case when t.prepodavatel_vakansiya then \'Вакансия\' else fl.familiya||\' \'||fl.imya||\' \'||fl.otchestvo end as fio_prepodavatelya,
                      k.tip as tip_kursa
                FROM
                  kurs as k
                  INNER JOIN razdel_kursa as rk on k.id = rk.kurs
                  INNER  JOIN podrazdel_kursa as pk on rk.id = pk.razdel
                  INNER JOIN tema as t on pk.id = t.podrazdel
                  LEFT JOIN rabota_po_teme as r on t.tip_raboty=r.id
                  LEFT JOIN fiz_lico as fl on t.prepodavatel_fiz_lico = fl.id
                WHERE t.id = :id';
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$id)->queryOne()){
//            $res['umks']=['umk_id'=>$res['umk_id'],
//                'umk_annotaciya'=>$res['umk_annotaciya'],
//                'umk_fajl'=>$res['umk_fajl'],
//                'umk_uri'=>$res['umk_uri']  ,
//                'umk_file_disk_name'=>$res['umk_file_disk_name'],
//                'umk_file_show_name' => $res['umk_file_show_name']
            //];
            return $res;
        }
        else return array();
    }

    public static function get_umk_by_id($id){
        $sql = 'SELECT umk.id as umk_id,umk.opisanie as umk_opisanie,umk.fajl as umk_fajl,
                      umk.uri as umk_uri, f.vnutrennee_imya_fajla as umk_file_disk_name,
                      f.vneshnee_imya_fajla as umk_file_show_name
                FROM
                umk
                LEFT JOIN fajl as f on umk.fajl = f.id
                where umk.id =:id';
        $umk = array();
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$id)->queryOne()){
            $umk = $res;
        }
        return $umk;
    }

    public  static function get_kf_by_theme_id($theme_id){
        $sql ='select t.id as theme_id,fk.id as theme_forma_kontrolya,fk.nazvanie as theme_forma_kontrolya_name
               from tema as t
               inner join forma_kontrolya_v_techenie_kursa as fk on t.forma_kontrolya = fk.id
               where t.id = :id';
        $kim = array();
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$theme_id)->queryOne()){
            $kim =$res;
        }
        return $kim;
    }
    
    public static function get_kim_by_id($kim_id){
        $sql = 'SELECT kt.id as kim_id,kt.opisanie as kim_opisanie,kt.fajl as kim_fajl,
                      kt.uri as kim_uri, kt.text as kim_text, f.vnutrennee_imya_fajla as kim_file_disk_name,
                      f.vneshnee_imya_fajla as kim_file_show_name
                FROM kim as kt
                LEFT JOIN fajl as f on kt.fajl = f.id
                where kt.id =:id';
        $kim = array();
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$kim_id)->queryOne()){
            $kim = $res;
        }
        return $kim;
    }

    public static function get_theme_dr_by_id($id){
        $sql = 'SELECT id, nazvanie as theme_dr_name FROM tema_diplomnoj_raboty_kursa where id = :id';
        $theme_dr = array();
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$id)->queryOne()){
            $theme_dr = $res;
        }
        return $theme_dr;
    }

    public static function get_itogovaya_attestaciya_by_kurs_id($kurs_id){
        $sql = 'SELECT k.id as kurs_id, k.chasy_itogovoj_attestacii as chasy,
                      k.nedelya_itogovoj_attestacii as nedelya,
                      fiak.nazvanie as forma_attestacii, fiak.id as fiak_id,
                      k.opisanie_itogovoj_attestacii as opisanie,
                      case when kpk.kontroliruyuschij_vakansiya then -1 else fz.id end as kontr_id,
                      case when kpk.kontroliruyuschij_vakansiya then \'Вакансия\'
                      else fz.familiya||\' \'||fz.imya||\' \'||fz.otchestvo end as kontr_fio,
                      k.tip as tip_kursa
                FROM kurs as k
                INNER JOIN forma_itogovoj_attestacii_kursa as fiak on k.forma_itogovoj_attestacii = fiak.id
                LEFT JOIN kontroliruyuschij_kursa as kpk on k.id = kpk.kurs
                LEFT JOIN fiz_lico as fz on kpk.kontroliruyuschij_fiz_lico=fz.id
                WHERE k.id = :id';
        $fiak = array();
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$kurs_id)->queryAll()){
            foreach($res as $k=>$v){
                if (!$fiak) {
                    $fiak = $v;
                    $fiak['kontrols']=[];
                }
                if ($v['kontr_id']) $fiak['kontrols'][$v['kontr_id']] = $v['kontr_fio'];
            }
        }
        return $fiak;
    }

    public static function get_podrazdel_by_id($id){
        $sql = 'SELECT pk.id,pk.nazvanie,k.tip as tip_kursa,rk.id as razdel_id,pk.nomer,
                       case when pk.rukovoditel_vakansiya then -1 else pk.rukovoditel end as rukovoditel_podrazdela_id,
                       case when pk.rukovoditel_vakansiya then \'Вакансия\' else fz.familiya||\' \'||fz.imya||\' \'||fz.otchestvo end as rukovoditel_podrazdela_fio,
                       COALESCE(fk.id,-1) as kf_podrazdel_id,
                       pk.chasy_kontrolya as chasy_kf_podrazdela,
                       pk.raschitano_chasov_lekcyj as podrazdel_lk,
                       pk.raschitano_chasov_praktik as podrazdel_pr,
                       pk.raschitano_chasov_srs as podrazdel_srs,
                       fk.nazvanie as kf_podrazdela_name,
                       pk.nedelya_nachalo as podrazdel_nedelya_nachalo,pk.nedelya_konec as podrazdel_nedelya_konec
                FROM kurs as k
                INNER JOIN razdel_kursa as rk on k.id = rk.kurs
                INNER JOIN podrazdel_kursa as pk on rk.id = pk.razdel
                LEFT JOIN fiz_lico as fz on pk.rukovoditel = fz.id
                LEFT JOIN forma_kontrolya_v_techenie_kursa as fk on pk.forma_kontrolya = fk.id
                where pk.id = :id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$id)->queryOne();
        $podrazdel = array();
        if ($res) $podrazdel = $res;
        return $podrazdel;

    }

    public static function get_kf_podrazdela_by_id($podrazdel_id){
        $sql = 'SELECT p.id, kf.nazvanie as kf_podrazdela_name,
                       p.chasy_kontrolya as chasy_kf_podrazdela,
                       kf.id as kf_podrazdel_id,
                       case when kontrol_pk.kontroliruyuschij_vakansiya then -1 else fz_kontrol.id end as kontrol_id,
                       case when kontrol_pk.kontroliruyuschij_vakansiya then \'Вакансия\'
                       else fz_kontrol.familiya||\' \'||fz_kontrol.imya||\' \'||fz_kontrol.otchestvo end as kontrol_fio,
                       k.tip as tip_kursa
                FROM podrazdel_kursa as p
                inner join forma_kontrolya_v_techenie_kursa as kf on p.forma_kontrolya = kf.id
                left join kontroliruyuschij_podrazdela_kursa as kontrol_pk on p.id = kontrol_pk.podrazdel_kursa
                left join fiz_lico as fz_kontrol on kontrol_pk.kontroliruyuschij_fiz_lico = fz_kontrol.id
                inner join razdel_kursa as rk on p.razdel = rk.id
                inner join kurs as k on rk.kurs = k.id
                where p.id = :id';
        $kf_podrazdela = [];
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$podrazdel_id)->queryAll()){
            foreach ($res as $k=>$v) {
              if (!$kf_podrazdela){
                  $kf_podrazdela = $v;
                  $kf_podrazdela['kontrol_fiz_lica'] = [];
              }
              if ($v['kontrol_id']) $kf_podrazdela['kontrol_fiz_lica'][$v['kontrol_id']] = $v['kontrol_fio'];
            }
        }
        return $kf_podrazdela;
    }

    public static function get_podrazdel_and_themes($kurs_id){
        $res = array();
        $sql = 'SELECT rk.tip as razdel_tip, t.id as theme_id, t.chasy as theme_chasy,
                      t.nazvanie as theme_nazvanie,t.soderzhanie as theme_soderzhanie,
                      t.forma_kontrolya as theme_forma_kontrolya, t.nedelya as theme_nedelya,
                      case when t.prepodavatel_vakansiya then -1 else t.prepodavatel_fiz_lico end as theme_prepodavatel,
                      t.nomer as theme_nomer,
                      t.tip_raboty as theme_tip_raboty,r.nazvanie as tip_rabot_name,
                      case when t.prepodavatel_vakansiya then \'Вакансия\' else fl.familiya||\' \'||fl.imya||\' \'||fl.otchestvo end as fio_prepodavatelya,
                      umk.id as umk_id,umk.opisanie as umk_opisanie,umk.fajl as umk_fajl,
                      umk.uri as umk_uri, f.vnutrennee_imya_fajla as umk_file_disk_name,
                      f.vneshnee_imya_fajla as umk_file_show_name,
                      fk.nazvanie as theme_forma_kontrolya_name,
                      kim.id as kim_id,kim.opisanie as kim_opisanie,kim.fajl as kim_fajl,
                      kim.uri as kim_uri, kim.text as kim_text, kimf.vnutrennee_imya_fajla as kim_file_disk_name,
                      kimf.vneshnee_imya_fajla as kim_file_show_name,
                      pk.id,k.id as kurs,pk.nomer,pk.nazvanie,
                      pk.razdel,pk.forma_kontrolya as kf_podrazdel_id,
                      pk.chasy_kontrolya as chasy_kf_podrazdela,
                      fk_porazdel.nazvanie as kf_podrazdela_name,
                      pk.raschitano_chasov_lekcyj as podrazdel_lk,
                      pk.raschitano_chasov_praktik as podrazdel_pr,
                      pk.raschitano_chasov_srs as podrazdel_srs,
                      pk.nedelya_nachalo as podrazdel_nedelya_nachalo,
                      pk.nedelya_konec as podrazdel_nedelya_konec,
                      kim_podrazdel.id as kim_pk_id,kim_podrazdel.opisanie as kim_pk_opisanie,kim_podrazdel.fajl as kim_pk_fajl,
                      kim_podrazdel.uri as kim_pk_uri, kim_podrazdel.text as kim_pk_text, kim_pk_f.vnutrennee_imya_fajla as kim_pk_file_disk_name,
                      kim_pk_f.vneshnee_imya_fajla as kim_pk_file_show_name,
                      umk_podrazdel.id as umk_pk_id,umk_podrazdel.opisanie as umk_pk_opisanie,umk_podrazdel.fajl as umk_pk_fajl,
                      umk_podrazdel.uri as umk_pk_uri, umk_pk_f.vnutrennee_imya_fajla as umk_pk_file_disk_name,
                      umk_pk_f.vneshnee_imya_fajla as umk_pk_file_show_name,
                      case when kontrol_pk.kontroliruyuschij_vakansiya then -1 else fz_kontrol.id end as kontrol_id,
                      case when kontrol_pk.kontroliruyuschij_vakansiya then \'Вакансия\'
                      else fz_kontrol.familiya||\' \'||fz_kontrol.imya||\' \'||fz_kontrol.otchestvo end as kontrol_fio,
                      k.tip as tip_kursa,rk.id as razdel_id,nk.nazvanie as razdel_nazvanie,rk.nazvanie as rk_nazvanie_id,
                      case when pk.rukovoditel_vakansiya then -1 else rukovoditel_podrazdela.id end as rukovoditel_podrazdela_id,
                      case when pk.rukovoditel_vakansiya then \'Вакансия\' else rukovoditel_podrazdela.familiya||\' \'||rukovoditel_podrazdela.imya||\' \'||rukovoditel_podrazdela.otchestvo end as rukovoditel_podrazdela_fio
                FROM
                  razdel_kursa as rk
                  left join nazvanie_dlya_razdela_kursa as nk on rk.nazvanie = nk.id
                  left join podrazdel_kursa as pk on rk.id = pk.razdel
                  LEFT JOIN tema as t on pk.id=t.podrazdel
                  LEFT JOIN rabota_po_teme as r on t.tip_raboty=r.id
                  LEFT JOIN fiz_lico as fl on t.prepodavatel_fiz_lico = fl.id
                  LEFT JOIN umk_temy as ut on t.id = ut.tema
                  LEFT JOIN umk as umk on ut.umk = umk.id
                  LEFT JOIN fajl as f on umk.fajl = f.id
                  LEFT JOIN forma_kontrolya_v_techenie_kursa as fk on t.forma_kontrolya = fk.id
                  LEFT JOIN kim_temy as kt on t.id = kt.tema
                  LEFT JOIN kim as kim on kt.kim = kim.id
                  LEFT JOIN fajl as kimf on kim.fajl=kimf.id
                  LEFT JOIN forma_kontrolya_v_techenie_kursa as fk_porazdel on pk.forma_kontrolya = fk_porazdel.id
                  left join kim_podrazdela_kursa as kim_pk on pk.id = kim_pk.podrazdel_kursa
                  left join kim as kim_podrazdel on kim_pk.kim = kim_podrazdel.id
                  left join fajl as kim_pk_f on kim_podrazdel.fajl = kim_pk_f.id
                  left join umk_podrazdela_kursa as umk_pk on pk.id = umk_pk.podrazdel_kursa
                  left join umk as umk_podrazdel on umk_pk.umk = umk_podrazdel.id
                  left join fajl as umk_pk_f on umk_podrazdel.fajl = umk_pk_f.id
                  left join kontroliruyuschij_podrazdela_kursa  as kontrol_pk on pk.id = kontrol_pk.podrazdel_kursa
                  left join fiz_lico as fz_kontrol on kontrol_pk.kontroliruyuschij_fiz_lico = fz_kontrol.id
                  inner join kurs as k on rk.kurs = k.id
                  left join fiz_lico as rukovoditel_podrazdela on pk.rukovoditel = rukovoditel_podrazdela.id
                WHERE k.id=:kurs_id
                ORDER BY rk.tip, rk.id, pk.nomer, t.nomer,umk.id,kim.id,kim_podrazdel.id';
        if  ($query = Yii::$app->db->createCommand($sql)->bindValue(':kurs_id',$kurs_id)->queryAll()) {
            foreach($query as $k=>$v){
                //var_dump($v['id']);die();
                if (!isset($res[$v['razdel_tip']])) $res[$v['razdel_tip']] = [];
                if (!isset($res[$v['razdel_tip']][$v['razdel_id']])) {
                    $res[$v['razdel_tip']][$v['razdel_id']] = ['razdel_id'=>$v['razdel_id'],
                                             'razdel_nazvanie'=>$v['razdel_nazvanie'],
                                             'rk_nazvanie_id'=>$v['rk_nazvanie_id'],
                                             'tip_kursa'=>$v['tip_kursa'],
                                             'razdel_tip'=>$v['razdel_tip'],
                                             'podrazdels'=>[]
                                            ];
                }
                if ($v['id']) {
                    if (!isset($res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']])) {
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']] = array();
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['nazvanie'] = $v['nazvanie'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['id'] = $v['id'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['razdel_id'] = $v['razdel_id'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['nomer'] = $v['nomer'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['tip_kursa'] = $v['tip_kursa'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['kf_podrazdel_id'] = $v['kf_podrazdel_id'] == null && $v['tip_kursa'] != 'pk' ? -1 :$v['kf_podrazdel_id'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['chasy_kf_podrazdela'] = $v['chasy_kf_podrazdela'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['kf_podrazdela_name'] = $v['kf_podrazdela_name'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['rukovoditel_podrazdela_id'] = $v['rukovoditel_podrazdela_id'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['rukovoditel_podrazdela_fio'] = $v['rukovoditel_podrazdela_fio'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_lk'] = $v['podrazdel_lk'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_pr'] = $v['podrazdel_pr'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_srs'] = $v['podrazdel_srs'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_nedelya_nachalo'] = $v['podrazdel_nedelya_nachalo'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_nedelya_konec'] = $v['podrazdel_nedelya_konec'];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_kims'] = [];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_umks'] = [];
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['kontrol_fiz_lica'] = [];

                    }
                    if ($v['kontrol_id'] and !isset($res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['kontrol_fiz_lica'][$v['kontrol_id']]))
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['kontrol_fiz_lica'][$v['kontrol_id']] = $v['kontrol_fio'];
                    if ($v['kim_pk_id'] and !isset($res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_kims'][$v['kim_pk_id']])) {
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_kims'][$v['kim_pk_id']] =
                            [
                                'kim_id' => $v['kim_pk_id'],
                                'kim_opisanie' => $v['kim_pk_opisanie'],
                                'kim_fajl' => $v['kim_pk_fajl'],
                                'kim_uri' => $v['kim_pk_uri'],
                                'kim_text' => $v['kim_pk_text'],
                                'kim_file_disk_name' => $v['kim_pk_file_disk_name'],
                                'kim_file_show_name' => $v['kim_pk_file_show_name'],
                                'tip_kursa'=>$v['tip_kursa'],
                                'tip'=>1
                            ];
                    }
                    if ($v['umk_pk_id'] and !isset($res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_umks'][$v['umk_pk_id']])) {
                        $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['podrazdel_umks'][$v['umk_pk_id']] =
                            [
                                'umk_id' => $v['umk_pk_id'],
                                'umk_opisanie' => $v['umk_pk_opisanie'],
                                'umk_fajl' => $v['umk_pk_fajl'],
                                'umk_uri' => $v['umk_pk_uri'],
                                'umk_file_disk_name' => $v['umk_pk_file_disk_name'],
                                'umk_file_show_name' => $v['umk_pk_file_show_name'],
                                'tip_kursa'=>$v['tip_kursa'],
                                'tip'=>1
                            ];
                    }
                    if ($v['theme_id']) {
                        if (!isset($res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']]))
                            $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']] = $v;
                        if (!isset($res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']]['umks']))
                            $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']]['umks'] = [];
                        if ($v['umk_id'] and !isset($res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']]['umks'][$v['umk_id']]))
                            $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']]['umks'][$v['umk_id']] = ['umk_id' => $v['umk_id'],
                                'umk_opisanie' => $v['umk_opisanie'],
                                'umk_fajl' => $v['umk_fajl'],
                                'umk_uri' => $v['umk_uri'],
                                'umk_file_disk_name' => $v['umk_file_disk_name'],
                                'umk_file_show_name' => $v['umk_file_show_name'],
                                'theme_id' => $v['theme_id'],
                                'tip_kursa'=>$v['tip_kursa'],
                                'tip'=>2
                            ];
                        if (!isset($res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']]['kims']))
                            $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']]['kims'] = [];
                        if ($v['kim_id'] and !isset($res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']]['kims'][$v['kim_id']]))
                            $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'][$v['theme_id']]['kims'][$v['kim_id']] = [
                                'kim_id' => $v['kim_id'],
                                'kim_opisanie' => $v['kim_opisanie'],
                                'kim_fajl' => $v['kim_fajl'],
                                'kim_uri' => $v['kim_uri'],
                                'kim_text' => $v['kim_text'],
                                'kim_file_disk_name' => $v['kim_file_disk_name'],
                                'kim_file_show_name' => $v['kim_file_show_name'],
                                'tip_kursa'=>$v['tip_kursa'],
                                'tip'=>2
                            ];

                        //theme_forma_kontrolya_name
                    } else $res[$v['razdel_tip']][$v['razdel_id']]['podrazdels'][$v['id']]['themes'] = array();
                }

            }
            //if ($res[''])
            //var_dump($query);
        }
        return $res;
    }

    public static function get_attestatciya($kurs_id){
//        kim_podrazdel.opisanie as kim_pk_opisanie,kim_podrazdel.fajl as kim_pk_fajl,
//                      kim_podrazdel.uri as kim_pk_uri, kim_podrazdel.text as kim_pk_text, kim_pk_f.vnutrennee_imya_fajla as kim_pk_file_disk_name,
//                      kim_pk_f.vneshnee_imya_fajla as kim_pk_file_show_name,
        $sql = 'SELECT k.id as kurs_id, k.chasy_itogovoj_attestacii as chasy,
                      k.nedelya_itogovoj_attestacii as nedelya,
                      fiak.nazvanie as forma_attestacii, fiak.id as fiak_id,
                      k.opisanie_itogovoj_attestacii as opisanie,
                      dr.id as theme_dr_id, dr.nazvanie as theme_dr_name,
                      case when kontroliruyuschij_vakansiya then -1 else fz.id end as kontr_id,
                      case when kontroliruyuschij_vakansiya then \'Вакансия\'
                      else fz.familiya||\' \'||fz.imya||\' \'||fz.otchestvo end as kontr_fio,
                      k.tip as tip_kursa,
                      kim.id as kim_id,
                      kim.opisanie as kim_opisanie,
                      kim.fajl as kim_fajl,
                      kim.uri as kim_uri,
                      kim.text as kim_text,
                      kimf.vnutrennee_imya_fajla as kim_file_disk_name,
                      kimf.vneshnee_imya_fajla as kim_file_show_name
                FROM kurs as k
                INNER JOIN forma_itogovoj_attestacii_kursa as fiak on k.forma_itogovoj_attestacii = fiak.id
                LEFT JOIN tema_diplomnoj_raboty_kursa as dr on k.id = dr.kurs
                LEFT JOIN kontroliruyuschij_kursa as kpk on k.id = kpk.kurs
                LEFT JOIN fiz_lico as fz on kpk.kontroliruyuschij_fiz_lico=fz.id
                LEFT JOIN kim_kursa as kk on k.id = kk.kurs
                LEFT JOIN kim on kk.kim =kim.id
                LEFT JOIN fajl as kimf on kim.fajl=kim.fajl
                WHERE k.id = :id';
        $attestaciya = array();
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$kurs_id)->queryAll()){
            foreach ($res as $k=>$v) {
                if (!isset($attestaciya['kurs_id'])) {
                    $attestaciya = $v;
                    $attestaciya['themes_dr'] = [];
                    $attestaciya['kontrols'] = [];
                    $attestaciya['kims'] = [];
                }
                if ($v['theme_dr_name']) $attestaciya['themes_dr'][] = ['id'=>$v['theme_dr_id'],'theme_dr_name'=>$v['theme_dr_name']];
                if ($v['kontr_id'] and !isset($attestaciya['kontrols'][$v['kontr_id']])) $attestaciya['kontrols'][$v['kontr_id']] = $v['kontr_fio'];
                if ($v['kim_id'] and !isset($attestaciya['kims'][$v['kim_id']])) $attestaciya['kims'][$v['kim_id']] =[
                    'kim_id' => $v['kim_id'],
                    'kim_opisanie' => $v['kim_opisanie'],
                    'kim_fajl' => $v['kim_fajl'],
                    'kim_uri' => $v['kim_uri'],
                    'kim_text' => $v['kim_text'],
                    'kim_file_disk_name' => $v['kim_file_disk_name'],
                    'kim_file_show_name' => $v['kim_file_show_name'],
                    'tip'=>3,
                    'tip_kursa'=>$v['tip_kursa']
                ];
            }
        }
        return $attestaciya;
        //--LEFT JOIN tema_diplomnoj_raboty_kursa as dr on k.id = dr.kurs
    }

    public static function is_razdel_have_podrazdels($razdel_id){
        $sql = 'SELECT count(*) as count FROM podrazdel_kursa WHERE razdel=:razdel';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':razdel',$razdel_id)->queryScalar();
        if ($res>0) return true;
        else false;
    }

    public static function is_podrazdel_have_themes($id){
        $sql = 'select count(*) as count from podrazdel_kursa as pk
                  inner join tema as t on pk.id = t.podrazdel
                where pk.id=:id';
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$id)->queryOne()){
            if ($res['count']>0) return true;
            else return false;
        }
        else return false;
    }

    public static function is_podrazdel_have_kf($id){
        $sql = 'SELECT forma_kontrolya FROM podrazdel_kursa WHERE id = :id';
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$id)->queryOne()){
            if ($res['forma_kontrolya']) return true;
        }
        return false;
    }

    public static function is_theme_have_umk_or_cc($theme_id){//есть ли у темы УМК или контрольные формы
        $sql = 'select t.forma_kontrolya, count(ut.*) as count from tema as t
                  left join umk_temy as ut on t.id = ut.tema
                where t.id = :id
                group by t.forma_kontrolya';
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$theme_id)->queryOne()){
            if ($res['count']>0 || $res['forma_kontrolya']) return true;
        }
        return false;
    }

    public static function is_kf_have_kim($theme_id){//есть ли у темы УМК или контрольные формы
        $sql = 'select count(kt.*) as count from tema as t
                 left join kim_temy as kt on t.id = kt.tema
                where t.id = :id
                group by t.forma_kontrolya';
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$theme_id)->queryOne()){
            if ($res['count']>0) return true;
        }
        return false;
    }

    public static function is_itgovaiya_attestatciya_have_themes_dr($kurs_id){
        $sql = 'select count(*) as count from tema_diplomnoj_raboty_kursa where kurs = :kurs';
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':kurs',$kurs_id)->queryOne()){
            if ($res['count']>0) return true;
        }
        return false;
    }

    public  static function get_max_week_of_kurs($kurs_id){
        $sql = 'SELECT case when max(t.nedelya) > coalesce(k.nedelya_itogovoj_attestacii,0) then
                       max(t.nedelya) else coalesce(k.nedelya_itogovoj_attestacii,0) end as max_nedelya
                FROM kurs as k
                LEFT JOIN razdel_kursa as rk on k.id = rk.kurs
                LEFT JOIN nazvanie_dlya_razdela_kursa as kn on rk.nazvanie = kn.id
                LEFT JOIN podrazdel_kursa as pk on rk.id = pk.razdel
                LEFT JOIN tema as t on pk.id = t.podrazdel
                WHERE k.id = :id
                GROUP BY k.id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$kurs_id)->queryScalar();
        if  ($res) return $res;
        else return false;
    }

    public static function get_kug($kurs_id){
        $sql = 'select pk.razdel,kn.nazvanie as razdel_nazvanie,pk.id as podrazdel_id,pk.nazvanie,t.id as tema_id,t.nazvanie as tema,
                  fk.nazvanie as forma_kontrolya_temi, t.nedelya as tema_nedelya,
                  podrazdel_fk.nazvanie as podrazdel_fk_name, pk.chasy_kontrolya as podrazdel_chasy_fk,
                  sum(case when t.tip_raboty=1 then t.chasy else 0  end) as lk,
                  sum(case when t.tip_raboty between 2 and 10 or t.tip_raboty=12 then t.chasy else 0 end) as pr,
                  sum(case when t.tip_raboty=11 then t.chasy else 0 end) as srs,
                  k.tip as tip_kursa,rk.nazvanie as tip_razdela,rk.tip as razdel_tip
                from kurs as k
                inner join razdel_kursa as rk on k.id = rk.kurs
                inner join nazvanie_dlya_razdela_kursa as kn on rk.nazvanie = kn.id
                inner join podrazdel_kursa as pk on rk.id = pk.razdel
                inner join tema as t on pk.id = t.podrazdel
                left join forma_kontrolya_v_techenie_kursa as fk on t.forma_kontrolya = fk.id
                left join forma_kontrolya_v_techenie_kursa as podrazdel_fk on pk.forma_kontrolya=podrazdel_fk.id
                where k.id=:kurs_id
                group by pk.razdel,pk.nazvanie,kn.nazvanie,pk.id,t.nomer,t.id,t.nazvanie,fk.nazvanie,podrazdel_fk.nazvanie,pk.chasy_kontrolya,k.tip,rk.nazvanie,rk.tip
                order by rk.tip,rk.nazvanie, pk.razdel,pk.nomer,t.nomer';
        $kug = array();
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':kurs_id',$kurs_id)->queryAll()){
            foreach ($res as $k=>$v) {
                if (!isset($kug[$v['razdel_tip']])) $kug[$v['razdel_tip']] = [];
                if (!isset($kug[$v['razdel_tip']][$v['razdel']])){
                    $kug[$v['razdel_tip']][$v['razdel']] = array();
                    $kug[$v['razdel_tip']][$v['razdel']]['nazvanie'] = $v['razdel_nazvanie'];
                    $kug[$v['razdel_tip']][$v['razdel']]['tip_razdela'] = $v['tip_razdela'];
                    $kug[$v['razdel_tip']][$v['razdel']]['tip_kursa'] = $v['tip_kursa'];
                    $kug[$v['razdel_tip']][$v['razdel']]['razdel_tip'] = $v['razdel_tip'];
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'] = [];
                }
                if (!isset($kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']])) {
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']] = array();
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['nazvanie'] = $v['nazvanie'];
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['podrazdel_fk_name'] = $v['podrazdel_fk_name'];
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['podrazdel_chasy_fk'] = $v['podrazdel_chasy_fk'];
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['themes'] = array();
                    //$kug[$v['razdel']][$v['podrazdel_id']]['forma_kontrolya_temi'] = $v['forma_kontrolya_temi'];
                }
                if ($v['tema_id']) {
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['themes'][$v['tema_id']]['nazvanie'] = $v['tema'];
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['themes'][$v['tema_id']]['lk'] = $v['lk'];
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['themes'][$v['tema_id']]['pr'] = $v['pr'];
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['themes'][$v['tema_id']]['srs'] = $v['srs'];
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['themes'][$v['tema_id']]['nedelya'] = $v['tema_nedelya'];
                    $kug[$v['razdel_tip']][$v['razdel']]['podrazdels'][$v['podrazdel_id']]['themes'][$v['tema_id']]['forma_kontrolya_temi'] = $v['forma_kontrolya_temi'];
                }
            }
        }
        return $kug;
    }

    public static function get_razdel_name($razdel){
        return '';
        $razdels=['r1'=>'Нормативно-правовые основы образовательной деятельности',
                  'r2'=>'Психолого-педагогические основы образовательной деятельности',
                  'r3'=>'Предметно-методические основы образовательной деятельности',
                  'var'=>'Вариативная часть'];
        return $razdels[$razdel];
    }

    public static function get_kims($kurs_id){
        $sql = 'select 1 as type,t.id as tema_id,t.nazvanie as tema,fkpt.nazvanie as forma_kontrolya,
                       f.vneshnee_imya_fajla as file_name, f.vnutrennee_imya_fajla as file_url,
                       kim.uri as url,kim.text as text,k.tip
                from kurs as k
                inner join razdel_kursa as rk on rk.kurs = k.id
                inner join podrazdel_kursa as pk on rk.id = pk.razdel
                inner join tema as t on pk.id = t.podrazdel
                inner join forma_kontrolya_v_techenie_kursa as fkpt on t.forma_kontrolya = fkpt.id
                inner join kim_temy as kt on t.id = kt.tema
                inner join kim on kt.kim = kim.id
                LEFT JOIN fajl as f on kim.fajl = f.id
                where k.id = :id
                UNION
                select 2 as type, att.id as tema_id, att.nazvanie as tema,att.nazvanie as forma_kontrolya,
                       f.vneshnee_imya_fajla as file_name, f.vnutrennee_imya_fajla as file_url,
                       kim.uri as url,kim.text as text,k.tip
                from kurs as k
                  inner join kim_kursa as kt on k.id = kt.kurs
                  inner join kim on kt.kim = kim.id
                  inner join forma_itogovoj_attestacii_kursa as att on k.forma_itogovoj_attestacii = att.id
                  LEFT JOIN fajl as f on kim.fajl = f.id
                where k.id = :id
                UNION
                select 2 as type, 0 as tema_id, att.nazvanie as tema,
                        att.nazvanie || \'<br>темы итоговых работ\' as forma_kontrolya,
                       \'\' as file_name,  \'\'as file_url,
                       \'\' as url,string_agg(t.nazvanie ,chr(13)||chr(10))  as text
                       ,k.tip
                from kurs as k
                  inner join tema_diplomnoj_raboty_kursa as t on k.id = t.kurs
                  inner join forma_itogovoj_attestacii_kursa as att on k.forma_itogovoj_attestacii = att.id
                where k.id = :id
                group by att.nazvanie, k.tip
                UNION
                select 3 as type,pk.id ,pk.nazvanie,fkpt.nazvanie as forma_kontrolya,
                       f.vneshnee_imya_fajla as file_name, f.vnutrennee_imya_fajla as file_url,
                       kim.uri as url,kim.text as text,k.tip
                from kurs as k
                inner join razdel_kursa as rk on rk.kurs = k.id
                inner join podrazdel_kursa as pk on rk.id = pk.razdel
                inner join forma_kontrolya_v_techenie_kursa as fkpt on pk.forma_kontrolya = fkpt.id
                inner join kim_podrazdela_kursa as kp on pk.id = kp.podrazdel_kursa
                inner join kim on kp.kim = kim.id
                LEFT JOIN fajl as f on kim.fajl = f.id
                where k.id = :id';
        $kims = array();
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$kurs_id)->queryAll()){
            $kims = $res;
        }
        return $kims;
    }

    public static function insert_umk($item){
        $umk_type = $item['umk_type'];
        if (!$file = $item['file']) $file = null;
        if (!$url = $item['url']) $url=null;
        if (!$opisanie = $item['opisanie']) $opisanie = null;
        $opisanie = ApiGlobals::to_trimmed_text($opisanie);
        if ($umk_type==1) $url = null;
        if ($umk_type==2) $file=null;
        $sql = 'INSERT INTO umk  (opisanie, fajl, uri)
                VALUES(:opisanie, :fajl, :uri)';
        $res = Yii::$app->db->createCommand($sql)
            ->bindValue(':opisanie',$opisanie)
            ->bindValue(':fajl',$file)
            ->bindValue(':uri',$url)
            ->execute();
        if ($res) return Yii::$app->db->getLastInsertID('umk_id_seq');
        else false;
    }

    public static function update_umk($umk){
        $umk_id = $umk['umk_id'];
        $umk_type = $umk['umk_type'];
        if (!$file = $umk['file']) $file = null;
        if (!$url = $umk['url']) $url=null;
        if (!$opisanie = $umk['opisanie']) $opisanie = null;
        $opisanie = ApiGlobals::to_trimmed_text($opisanie);
        if ($umk_type==1) $url = null;
        if ($umk_type==2) $file=null;
        $sql = 'UPDATE umk SET opisanie=:opisanie,fajl=:fajl,uri=:uri where id=:id';
        $res = Yii::$app->db->createCommand($sql)
            ->bindValue(':id',$umk_id)
            ->bindValue(':opisanie',$opisanie)
            ->bindValue(':fajl',$file)
            ->bindValue(':uri',$url)
            ->execute();
        if ($res) return true;
        else false;
    }

    public static function delete_umk($umk_id){
        $sql = 'DELETE FROM umk where id = :id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$umk_id)->execute();
        if ($res) return true;
        else return false;
    }

    public static function insert_kim($item){
        if (!$kim_opisanie =  $_POST['kim_opisanie']) $kim_opisanie = null;
        //file_put_contents('1.txt',print_r($_POST,true));
        $type_kim = $_POST['type_kim'];
        $kim_file = $_POST['kim_file'];
        $kim_url = $_POST['kim_url'];
        $kim_text = $_POST['kim_text'];
        $kim_opisanie = ApiGlobals::to_trimmed_text($kim_opisanie);
        if ($type_kim==1) {
            $kim_url = null;
            $kim_text = null;
        }
        elseif ($type_kim==2){
            $kim_file = null;
            $kim_text = null;
        }
        else {
            $kim_file = null;
            $kim_url = null;
        }
        $kim_text = ApiGlobals::to_trimmed_text($kim_text);
        $sql = 'INSERT INTO kim (opisanie, fajl, uri, text)
                        VALUES (:opisanie, :fajl, :uri, :text)';
        $res = Yii::$app->db->createCommand($sql)
                            ->bindValue(':opisanie',$kim_opisanie)
                            ->bindValue(':fajl',$kim_file)
                            ->bindValue(':uri',$kim_url)
                            ->bindValue(':text',$kim_text)
                ->execute();
        if ($res) return Yii::$app->db->getLastInsertID('kim_id_seq');
        else false;
    }

    public static function update_kim($kim){
        $kim_id = $kim['kim_id'];
        if (!$opisanie = $kim['opisanie']) $opisanie = null;
        $type_kim = $kim['type_kim'];
        $kim_file = $kim['file_kim'];
        $kim_url = $kim['kim_url'];
        $kim_text = $kim['kim_text'];
        if ($type_kim==1) {
            $kim_url = null;
            $kim_text = null;
        }
        elseif ($type_kim==2){
            $kim_file = null;
            $kim_text = null;
        }
        else {
            $kim_file = null;
            $kim_url = null;
        }
        $opisanie = ApiGlobals::to_trimmed_text($opisanie);
        $kim_text = ApiGlobals::to_trimmed_text($kim_text);
        $sql = 'UPDATE kim SET fajl = :fajl, uri = :uri, opisanie = :opisanie,text = :text where id = :id';
        $res = Yii::$app->db->createCommand($sql)
                            ->bindValue(':fajl',$kim_file)
                            ->bindValue(':uri',$kim_url)
                            ->bindValue(':opisanie',$opisanie)
                            ->bindValue(':text',$kim_text)
                            ->bindValue(':id',$kim_id)
                ->execute();
        if ($res) return true;
        else return false;
    }

    public static function delete_kim($kim_id){
        $sql = 'DELETE FROM kim where id = :id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$kim_id)->execute();
        if ($res) return true;
        else return false;
    }

    public static function kim_types(){
        return ['1'=>'файл','2'=>'ссылка','3'=>'текст'];
    }

     public static function get_kim_list_item($item){
         $content = '';
         if ($item['type']==1) {
             $content = '<p class="center">К теме "' . $item['tema'] . '"</p>';
         }
         else{
             $content = '<p class="center">К итоговой аттестации</p>';
         }
         $content .= '<p class="center"><b>'.ApiGlobals::first_letter_up($item['forma_kontrolya']).'</b></p>';

         if ($item['url']) $content.= ' - '.Html::a($item['url'],$item['url']);
         if ($item['file_url']) $content .= ' - '.Html::a($item['file_name'], Url::to(ApiGlobals::get_user_dir_url().$item['file_url']));
         if ($item['text']) $content .= ApiGlobals::parse_plain_text_to_html($item['text']);
         return $content;
     }

     public static function is_podrazdel_var($podrazdel_id){
         $sql = 'SELECT count(*) FROM
                 razdel_kursa as rk
                 INNER JOIN podrazdel_kursa as pk on rk.id = pk.razdel
                 where pk.id = :id and rk.nazvanie=7';
         $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$podrazdel_id)->queryScalar();
         if ($res > 0) return true;
         else return false;
     }

    //public static function is_first_podrazdel($kurs_id){
//        $sql = 'SELECT id FROM razdel_kursa as rk
//                INNER JOIN podrazdel_kursa as pk on rk.id = pk.razdel
//                WHERE rk.kurs = :kurs_id and rk.nazvanie=7';
//    }

     public static function get_sum_hours_of_first_var_podrazdel($kurs_id){
        $sql = 'select pk.id, sum(coalesce(t.chasy,0)) as chasy,
                  sum(case when t.tip_raboty=1 then t.chasy else 0  end) as lk,
                  sum(case when t.tip_raboty between 2 and 10 or t.tip_raboty=12 then t.chasy else 0 end) as pr,
                  sum(case when t.tip_raboty=11 then t.chasy else 0 end) as srs
                from
                razdel_kursa as rk
                INNER JOIN podrazdel_kursa as pk on rk.id = pk.razdel
                LEFT JOIN tema as t on pk.id = t.podrazdel
                where rk.kurs = :kurs and rk.nazvanie=7
                group by pk.id
                ORDER BY pk.nomer
                limit 1';
        $hours = ['podrazdel_id'=>'-1','hours'=>0,'lk'=>0,'pr'=>0,'srs'=>0];
        $res = Yii::$app->db->createCommand($sql)->bindValue(':kurs',$kurs_id)->queryOne();
        if ($res){
            $hours['podrazdel_id'] = $res['id'];
            $hours['hours'] = $res['chasy'];
            $hours['lk'] = $res['lk'];
            $hours['pr'] = $res['pr'];
            $hours['srs'] = $res['srs'];
        }
        return $hours;
     }

    public static function get_kurs_by_podrazdel($podrazdel_id){
        $sql = 'SELECT rk.kurs FROM
                razdel_kursa as rk
                INNER JOIN podrazdel_kursa as pk on rk.id = pk.razdel
                where pk.id = :id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$podrazdel_id)->queryScalar();
        return $res;
    }

    public static function get_podrazdel_by_tema($tema_id){
        $sql = 'SELECT podrazdel FROM tema where id = :id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$tema_id)->queryScalar();
        return $res;
    }

    public static function get_sum_hours_of_podrazdel($podrazdel_id,$theme_id = false){
        $sql = 'SELECT SUM(coalesce(t.chasy,0)) as chasy,
                  sum(case when t.tip_raboty=1 then t.chasy else 0  end) as lk,
                  sum(case when t.tip_raboty between 2 and 10 or t.tip_raboty=12 then t.chasy else 0 end) as pr,
                  sum(case when t.tip_raboty=11 then t.chasy else 0 end) as srs
                FROM podrazdel_kursa as pk
                left join tema as t on pk.id = t.podrazdel
                where pk.id = :id';
        if ($theme_id){
            $sql.=' and t.id!=:tema_id';
        }
        $command = Yii::$app->db->createCommand($sql)->bindValue(':id',$podrazdel_id);
        if ($theme_id) $command->bindValue(':tema_id',$theme_id);
        $res = $command->queryOne();
        return $res;
    }

    public static function get_sum_hours_of_podrazdel_wo_cur($tema_id){
        $sql = 'SELECT SUM(coalesce(t.chasy,0)) as chasy FROM podrazdel_kursa as pk
                left join tema as t on pk.id = t.podrazdel
                where pk.id in (select podrazdel from tema where id = :id) and t.id != :id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$tema_id)->queryScalar();
        return $res;
    }

    public static function get_razdels(){
        $sql = 'select * from nazvanie_dlya_razdela_kursa';
        $res = Yii::$app->db->createCommand($sql)->queryAll();
        $razdels= [];
        if ($res) {
            foreach ($res as $k=>$v) {
                $razdels[$v['id']] = $v['nazvanie'];
            }
        }
        return $razdels;
    }

    public static function is_razdel_already_added($kurs_id,$nazvanie_razdel_id){
        $sql = 'SELECT count(*) as count FROM razdel_kursa WHERE kurs=:kurs and nazvanie=:nazvanie';
        $res = Yii::$app->db->createCommand($sql)
                            ->bindValue(':kurs',$kurs_id)
                            ->bindValue(':nazvanie',$nazvanie_razdel_id)
                ->queryScalar();
        if ($res>0) return true;
        else return false;
    }

    public static function is_var_razdel_has_error($kurs_id){
        $sql = 'select pk.id, sum(coalesce(t.chasy,0)) as chasy,
                  sum(case when t.tip_raboty=1 then t.chasy else 0  end) as lk,
                  sum(case when t.tip_raboty between 2 and 10 or t.tip_raboty=12 then t.chasy else 0 end) as pr,
                  sum(case when t.tip_raboty=11 then t.chasy else 0 end) as srs
                from
                razdel_kursa as rk
                INNER JOIN podrazdel_kursa as pk on rk.id = pk.razdel
                LEFT JOIN tema as t on pk.id = t.podrazdel
                where rk.kurs = :kurs and rk.nazvanie=7
                group by pk.id
                ORDER BY pk.id
                ';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':kurs',$kurs_id)->queryAll();
        $is_error=false;
        foreach ($res as $k=>$v) {
            if ($res[0]['chasy']!=$v['chasy'] || $res[0]['lk']!=$v['lk'] || $res[0]['pr']!=$v['pr'] || $res[0]['srs']!=$v['srs']){
                $is_error = true;
                break;
            }
        }
        return $is_error;
    }

    public static function set_kurs_status($kurs_id,$status){
        //file_put_contents('1.txt',$status);
        $sql = 'UPDATE kurs SET status_programmy = :status where id = :id';
        $res = Yii::$app->db->createCommand($sql)
                            ->bindValue(':status',$status)
                            ->bindValue(':id',$kurs_id)
                ->execute();
        return $res;
    }

    public static function get_kurs($id){
        $sql = 'SELECT k.*,string_agg(ks.nazvanie,\', \') as kategorii,
                       fz.familiya||\' \'||fz.imya||\' \'||fz.otchestvo as rukovoditel,
                       fz.familiya as rukovoditel_familiya,
                       fz.imya as rukovoditel_imya,
                       fz.otchestvo as rukovoditel_otchestvo,
                       a.nazvanie as nazvanie_itogovoi_attestacii,
                       case k.formy_obucheniya when \'{ochnaya}\' then \'очная\' else \'заочная\' end as forma_obucheniya_kursa,
                       sp.nazvanie as podrazdelenie,sp.sokrashennoe_nazvanie as podrazdelenie_sokrashennoe_nazvanie,
                       d.nazvanie as rukovoditel_dolzhnost
                FROM kurs as k
                LEFT JOIN kategoriya_slushatelya_kursa as ksk on k.id = ksk.kurs
                LEFT JOIN kategoriya_slushatelya as ks on ksk.kategoriya_slushatelya = ks.id
                INNER JOIN fiz_lico as fz on k.rukovoditel = fz.id
                left join forma_itogovoj_attestacii_kursa as a on k.forma_itogovoj_attestacii = a.id
                inner join strukturnoe_podrazdelenie as sp on k.strukturnoe_podrazdelenie=sp.id
                inner join rabota_fiz_lica as rfl on rfl.fiz_lico = fz.id
                inner join dolzhnost_fiz_lica_na_rabote as dfl on rfl.id = dfl.rabota_fiz_lica
                inner join dolzhnost as d on dfl.dolzhnost = d.id
                WHERE k.id = :kurs_id
                GROUP BY k.id,fz.familiya,fz.imya,fz.otchestvo,a.nazvanie,sp.nazvanie,sp.sokrashennoe_nazvanie,d.nazvanie';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':kurs_id',$id)->queryOne();
        $kurs=[];
        if ($res) $kurs = $res;
        return $kurs;
    }

    public static function get_soderzhanie($kurs_id){
        $sql = 'SELECT nk.nazvanie as razdel_nazvanie,
                       pk.nazvanie as podrazdel_nazvanie,
                       pk.id as podrazdel_id,
                       t.id as tema_id,
                       t.nazvanie as tema_nazvanie,
                       t.soderzhanie as tema_soderzhanie,
                       rpt.nazvanie as tema_tip_rabot,
                       t.chasy as tema_chasy,
                       case when t.prepodavatel_vakansiya then \'Вакансия\'
                       else fz.familiya || \' \' || upper(substring(fz.imya  from 1 for 1)) || \'.\' || \' \' || upper(substring(fz.otchestvo from 1 for 1)) || \'.\' end as prepodavatel,
                       fk.nazvanie as tema_forma_kontrolya,
                       t.forma_kontrolya as forma_kontrolya_id,
                       t.tip_raboty as tema_tip_rabot_id
                FROM kurs as k
                LEFT JOIN razdel_kursa as rk on k.id = rk.kurs
                LEFT JOIN nazvanie_dlya_razdela_kursa as nk on rk.nazvanie = nk.id
                LEFT JOIN podrazdel_kursa as pk on rk.id = pk.razdel
                LEFT JOIN tema as t on pk.id = t.podrazdel
                LEFT JOIN rabota_po_teme as rpt on t.tip_raboty = rpt.id
                LEFT JOIN fiz_lico as fz on t.prepodavatel_fiz_lico = fz.id
                LEFT JOIN forma_kontrolya_v_techenie_kursa as fk on t.forma_kontrolya = fk.id
                WHERE k.id = :kurs_id
                ORDER BY rk.id,pk.nomer,t.nomer';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':kurs_id',$kurs_id)->queryAll();
        $s = [];
        foreach ($res as $k=>$v) {
            if (!isset($s[$v['razdel_nazvanie']])){
                $s[$v['razdel_nazvanie']] = [];
            }
            if (!isset($s[$v['razdel_nazvanie']][$v['podrazdel_id']])){
                $s[$v['razdel_nazvanie']][$v['podrazdel_id']] = ['nazvanie'=>$v['podrazdel_nazvanie'],
                                                                 'temy'=>[]];
            }
            if (!isset($s[$v['razdel_nazvanie']][$v['podrazdel_id']]['temy'][$v['tema_id']])){
                $s[$v['razdel_nazvanie']][$v['podrazdel_id']]['temy'][$v['tema_id']] = ['nazvanie'=>$v['tema_nazvanie'],
                                                                                        'soderzhanie'=>$v['tema_soderzhanie'],
                                                                                        'chasy'=>$v['tema_chasy'],
                                                                                        'prepodavatel'=>$v['prepodavatel'],
                                                                                        'forma_kontrolya'=>$v['tema_forma_kontrolya'],
                                                                                        'forma_kontrolya_id'=>$v['forma_kontrolya_id'],
                                                                                        'tip_rabot' => $v['tema_tip_rabot'],
                                                                                        'tip_rabot_id'=>$v['tema_tip_rabot_id']
                                                                                       ];
            }
        }
        return $s;
    }

    /**
     * @param $params - массиа парметров:
     * week_num - общее количство недель
     * is_head - если true то вернет строку для заголовка таблицы
     * cur_week - неделя текущей записи
     * chasy - часы текущйе записи
     */
    public static function get_week_row($params){ //возвращает html с неделями для одно строки КУГА
        $html = '';
        $begin = isset($params['begin']) ? $params['begin'] : 1;
        for($i=$begin;$i<=$params['week_num'];$i++) {
            if (isset($params['is_head']) and $params['is_head']) $html .= '<td class="center">'.$i.'</td>';
            elseif (isset($params['cur_week']))
                $html .= '<td class="center">'.($i == $params['cur_week'] ? $params['chasy'] : '').'</td>';
            elseif (isset($params['weekly_hours']))
                $html .= '<td class="center">'.$params['weekly_hours'][$i].'</td>';
            else $html.='<td></td>';
        }
        return $html;
    }

    public static function get_tip_razdela_name($tip){
        $razdel_types = ['baz'=>'Базовая часть','prof'=>'Профильная часть'];
        return $razdel_types[$tip];
    }

    public static function get_kug_html($kug=[],$attestaciya=[],$max_week_num = 0){
        $weekly_hours = [];//масси, в котром будет накапливаться недельные часы
        for($i=1;$i<=$max_week_num;$i++) $weekly_hours[$i] = 0;
        $kug_tb = '<table class="tb">
        <thead>
        <tr class="thead">
           <td rowspan="2">Номер</td>
           <td  rowspan="2">Наименование</td>
           <td  rowspan="2">Всего часов</td>
           <td colspan="3">В том числе</td>
           <td  rowspan="2">Форма контроля</td>
           '.($max_week_num ? '<td class="center" colspan="'.$max_week_num.'">Неделя</td>' : '').'
        </tr>
        <tr class="thead">
          <td>ЛК</td>
          <td>ПР</td>
          <td>СРС</td>
          '.($max_week_num ? KursGlobals::get_week_row(['week_num'=>$max_week_num,'is_head'=>true]) : '').'
        </tr>
        </thead>
        <tbody>';

        $all_lk = 0;
        $all_pr = 0;
        $all_srs = 0;

//        $current_razdel_tip = '';

        $razdel_num=1;

        foreach ($kug as $tip_razdela => $razdeli){
            $razdels='';

            foreach ($razdeli as $k => $v){

                $razdel_lk=0;
                $razdel_pr = 0;
                $razdel_srs = 0;

                $podrazdeli = '';
                $podrazdeli_s_temami = '';
                $for_kug = '';
                $for_plan ='';
                $podrazdel_num =1;

                $first_podrazdel = key($v['podrazdels']);

                foreach ($v['podrazdels'] as $pr => $pri){
                    $podrazdel_lk=0;
                    $podrazdel_pr = 0;
                    $podrazdel_srs = 0;
                    $podrazdel_kontrol=[];
                    $tem_num = 1;
                    $temi ='';
                    //var_dump($pri);
                    //$first_elem = reset($pri['themes']);
                    foreach($pri['themes'] as $tema_key => $tema_item){
                        $week_hours = $tema_item['lk']+$tema_item['pr']+$tema_item['srs'];
                        $temi .=  '<tr>
                            <td>'.$razdel_num.'.'.$podrazdel_num.'.'.$tem_num.'.</td>
                            <td>'.$tema_item['nazvanie'].'</td>
                            <td class="center">'.($week_hours ? $week_hours : '').'</td>
                            <td class="center">'.($tema_item['lk'] ? $tema_item['lk'] : '').'</td>
                            <td class="center">'.($tema_item['pr'] ? $tema_item['pr'] : '').'</td>
                            <td class="center">'.($tema_item['srs'] ? $tema_item['srs'] : '').'</td>
                            <td class="center">'.(isset($tema_item['forma_kontrolya_temi']) ? $tema_item['forma_kontrolya_temi'] : '').'</td>
                            '.($max_week_num ? KursGlobals::get_week_row(['week_num'=>$max_week_num,'cur_week'=>$tema_item['nedelya'],'chasy'=>$week_hours]) : '').'
                        </tr>';
                        //var_dump($temi);
                        if (isset($tema_item['forma_kontrolya_temi'])) $podrazdel_kontrol[] = $tema_item['forma_kontrolya_temi'];
                        //if ($pri['razdel']=='var' and )
                        $podrazdel_lk += $tema_item['lk'];
                        $podrazdel_pr += $tema_item['pr'];
                        $podrazdel_srs += $tema_item['srs'];
                        if (($v['tip_razdela']==7 and  $first_podrazdel==$pr) or $v['tip_razdela']!=7) $weekly_hours[$tema_item['nedelya']] += $week_hours;
                        $tem_num++;
                    }
                    $podrzdel_kf='';
                    if ($pri['podrazdel_fk_name']){
                        $podrzdel_kf =  '<tr class="sub2head">
                            <td></td>
                            <td>Итоговая аттестация по '.($v['tip_kursa']=='pk' ? 'блоку тем' : 'дисциплине').'</td>
                            <td class="center">'.($pri['podrazdel_chasy_fk'] ? $pri['podrazdel_chasy_fk'] : '').'</td>
                            <td class="center"></td>
                            <td class="center">'.($pri['podrazdel_chasy_fk'] ? $pri['podrazdel_chasy_fk'] : '').'</td>
                            <td class="center"></td>
                            <td class="center">'.$pri['podrazdel_fk_name'].'</td>
                            '.($max_week_num ? KursGlobals::get_week_row(['week_num'=>$max_week_num]) : '').'
                        </tr>';
                        $podrazdel_pr += $pri['podrazdel_chasy_fk'];
                    }
                    $podrazdeli = '<tr class="sub2head">
                            <td>'.$razdel_num.'.'.$podrazdel_num.'.</td>
                            <td>'.$pri['nazvanie'].'</td>
                            <td class="center">'.(($podrazdel_lk+$podrazdel_pr+$podrazdel_srs) ? ($podrazdel_lk+$podrazdel_pr+$podrazdel_srs) : '').'</td>
                            <td class="center">'.($podrazdel_lk ? $podrazdel_lk : '').'</td>
                            <td class="center">'.($podrazdel_pr ? $podrazdel_pr : '').'</td>
                            <td class="center">'.($podrazdel_srs ? $podrazdel_srs : '').'</td>
                            <td class="center">'.implode(',',$podrazdel_kontrol).'</td>
                            '.($max_week_num ? KursGlobals::get_week_row(['week_num'=>$max_week_num]) : '').'
                        </tr>';
                    $podrazdeli_s_temami = $podrazdeli.$temi.$podrzdel_kf;
                    $for_kug .=$podrazdeli_s_temami;
                    if (($v['tip_razdela']==7 and  $first_podrazdel==$pr) or $v['tip_razdela']!=7) {
                        $razdel_lk += $podrazdel_lk;
                        $razdel_pr += $podrazdel_pr;
                        $razdel_srs += $podrazdel_srs;
                    }
                    $podrazdel_num++;
                }
                $razdels .= '<tr class="subhead razdel-tr">
                            <td></td>
                            <td>'.$v['nazvanie'].'</td>
                            <td class="center">'.(($razdel_lk+$razdel_pr+$razdel_srs) ? ($razdel_lk+$razdel_pr+$razdel_srs) : '').'</td>
                            <td class="center">'.($razdel_lk ? $razdel_lk : '').'</td>
                            <td class="center">'.($razdel_pr ? $razdel_pr : '').'</td>
                            <td class="center">'.($razdel_srs ? $razdel_srs : '').'</td>
                            <td></td>
                            '.($max_week_num ? KursGlobals::get_week_row(['week_num'=>$max_week_num]) : '').'
                        </tr>';
                $razdels .= $for_kug;
                $razdel_num++;
                $all_lk+=$razdel_lk;
                $all_pr+=$razdel_pr;
                $all_srs+=$razdel_srs;
            }
            $kug_tb .='<tr>
                        <td class="center" colspan="7">'.KursGlobals::get_tip_razdela_name($tip_razdela).'</td>
                        '.($max_week_num ? KursGlobals::get_week_row(['week_num'=>$max_week_num]) : '').'
                       </tr>';
            $kug_tb .= $razdels;
        }


        if ($attestaciya){
            $kug_tb .= '<tr class="subhead attestatsiya-tr">
                            <td></td>
                            <td>Итоговая аттестация</td>
                            <td class="center">'.($attestaciya['chasy'] ? $attestaciya['chasy'] : '').'</td>
                            <td class="center"></td>
                            <td class="center">'.($attestaciya['chasy'] ? $attestaciya['chasy'] : '').'</td>
                            <td class="center"></td>
                            <td class="center">'.$attestaciya['forma_attestacii'].'</td>
                            '.($max_week_num ? KursGlobals::get_week_row(['week_num'=>$max_week_num,'cur_week'=>$attestaciya['nedelya'],'chasy'=>$attestaciya['chasy']]) : '').'
                        </tr>';
            $all_pr+=$attestaciya['chasy'];
            $weekly_hours[$attestaciya['nedelya']] += $attestaciya['chasy'];
        }
        $kug_tb .= '<tr class="summary bold-tr">
                        <td></td>
                        <td>Итого</td>
                        <td class="center">'.(($all_lk+$all_pr+$all_srs) ? ($all_lk+$all_pr+$all_srs) : '').'</td>
                        <td class="center">'.($all_lk ? $all_lk : '').'</td>
                        <td class="center">'.($all_pr ? $all_pr : '').'</td>
                        <td class="center">'.($all_srs ? $all_srs : '').'</td>
                        <td></td>
                        '.($max_week_num ? KursGlobals::get_week_row(['week_num'=>$max_week_num,'weekly_hours'=>$weekly_hours]) : '').'
                    </tr>';

        $kug_tb .= '</tbody></table>';

        return $kug_tb;
    }

    public static function get_uchebnii_plan_html($kug=[],$attestaciya=[]){
        //var_dump($kug);
        $plan = '<table class="tb tb_plan">
        <thead>
        <tr class="thead">
           <td rowspan="2">Номер</td>
           <td  rowspan="2">Наименование</td>
           <td  rowspan="2">Всего часов</td>
           <td colspan="3">В том числе</td>
           <td  rowspan="2">Форма контроля</td>
        </tr>
        <tr class="thead">
          <td>ЛК</td>
          <td>ПР</td>
          <td>СРС</td>
        </tr>
        </thead>
        <tbody>';

        $all_lk = 0;
        $all_pr = 0;
        $all_srs = 0;

        $razdel_num=1;
        foreach ($kug as $tip_razdela => $razdeli) {
            $razdels = '';
            foreach ($razdeli as $k => $v){

                $razdel_lk=0;
                $razdel_pr = 0;
                $razdel_srs = 0;

                $podrazdeli = '';
                $podrazdeli_s_temami = '';
                $for_kug = '';
                $for_plan ='';
                $podrazdel_num =1;

                $first_podrazdel = key($v['podrazdels']);

                foreach ($v['podrazdels'] as $pr => $pri){
                    $podrazdel_lk=0;
                    $podrazdel_pr = 0;
                    $podrazdel_srs = 0;
                    $podrazdel_kontrol=[];
                    $tem_num = 1;
                    $temi ='';
                    foreach($pri['themes'] as $tema_key => $tema_item){
                        if (isset($tema_item['forma_kontrolya_temi'])) $podrazdel_kontrol[] = $tema_item['forma_kontrolya_temi'];
                        $podrazdel_lk += $tema_item['lk'];
                        $podrazdel_pr += $tema_item['pr'];
                        $podrazdel_srs += $tema_item['srs'];
                        $tem_num++;
                    }
                    $podrzdel_kf='';
                    if ($pri['podrazdel_fk_name']){
                        $podrzdel_kf = $pri['podrazdel_fk_name'] ? $pri['podrazdel_fk_name'].'('.$pri['podrazdel_chasy_fk'].' ч.)' : '';
                        $podrazdel_kontrol[] = $pri['podrazdel_fk_name'];
//                        $podrzdel_kf =  '<tr class="sub2head">
//                            <td></td>
//                            <td>Контроль по '.($v['tip_kursa']=='pk' ? 'блоку тем' : 'дисциплине').'</td>
//                            <td class="center">'.($pri['podrazdel_chasy_fk'] ? $pri['podrazdel_chasy_fk'] : '').'</td>
//                            <td class="center"></td>
//                            <td class="center">'.($pri['podrazdel_chasy_fk'] ? $pri['podrazdel_chasy_fk'] : '').'</td>
//                            <td class="center"></td>
//                            <td class="center">'.($pri['podrazdel_fk_name'] ? $pri['podrazdel_fk_name'] : '').'</td>
//                        </tr>';
                        $podrazdel_pr += $pri['podrazdel_chasy_fk'];
                    }
                    $podrazdeli = '<tr class="sub2head">
                            <td>'.$razdel_num.'.'.$podrazdel_num.'.</td>
                            <td>'.$pri['nazvanie'].'</td>
                            <td class="center">'.(($podrazdel_lk+$podrazdel_pr+$podrazdel_srs) ? $podrazdel_lk+$podrazdel_pr+$podrazdel_srs : '').'</td>
                            <td class="center">'.($podrazdel_lk ?$podrazdel_lk : '').'</td>
                            <td class="center">'.($podrazdel_pr ? $podrazdel_pr : '').'</td>
                            <td class="center">'.($podrazdel_srs ? $podrazdel_srs : '').'</td>
                            <td class="center">'.$pri['podrazdel_fk_name'].'</td>
                        </tr>';
                    //$podrazdeli .= $podrzdel_kf;
                    $for_plan .= $podrazdeli;
                    if (($v['tip_razdela']==7 and  $first_podrazdel==$pr) or $v['tip_razdela']!=7) {
                        $razdel_lk += $podrazdel_lk;
                        $razdel_pr += $podrazdel_pr;
                        $razdel_srs += $podrazdel_srs;
                    }
                    $podrazdel_num++;
                }

                $razdels .= '<tr class="subhead  razdel-tr">
                            <td></td>
                            <td>'.$v['nazvanie'].'</td>
                            <td class="center">'.(($razdel_lk+$razdel_pr+$razdel_srs) ? ($razdel_lk+$razdel_pr+$razdel_srs) : '').'</td>
                            <td class="center">'.($razdel_lk ? $razdel_lk :'').'</td>
                            <td class="center">'.($razdel_pr ? $razdel_pr : '').'</td>
                            <td class="center">'.($razdel_srs ? $razdel_srs: '').'</td>
                            <td></td>
                        </tr>';

                $razdels .= $for_plan;
                $razdel_num++;
                $all_lk+=$razdel_lk;
                $all_pr+=$razdel_pr;
                $all_srs+=$razdel_srs;
            }
            $plan .='<tr>
                        <td class="center" colspan="7">'.KursGlobals::get_tip_razdela_name($tip_razdela).'</td>
                     </tr>';
            $plan .= $razdels;
        }

        if ($attestaciya){
            $plan .= '<tr class="subhead  razdel-tr">
                            <td></td>
                            <td>Итоговая аттестация</td>
                            <td class="center">'.($attestaciya['chasy'] ? : '').'</td>
                            <td class="center"></td>
                            <td class="center">'.($attestaciya['chasy'] ? $attestaciya['chasy'] :'').'</td>
                            <td class="center"></td>
                            <td class="center">'.$attestaciya['forma_attestacii'].'</td>
                        </tr>';
            $all_pr+=$attestaciya['chasy'];
        }
        $plan .= '<tr class="summary  razdel-tr">
                        <td></td>
                        <td>Итого</td>
                        <td class="center">'.(($all_lk+$all_pr+$all_srs) ? ($all_lk+$all_pr+$all_srs) : '').'</td>
                        <td class="center">'.($all_lk ? $all_lk : '').'</td>
                        <td class="center">'.($all_pr ? $all_pr : '').'</td>
                        <td class="center">'.($all_srs ? $all_srs : '').'</td>
                        <td></td>
                    </tr>';

        $plan .= '</tbody></table>';
        return $plan;
    }

    public static function get_soderzhanie_html($s){
        $result='';
        $razdel = 1;
        $razdely = '';
        foreach ($s as $k=>$v) {
            $all=0;
            $podrazdel =1;
            $podrazdely = '';
            foreach ($v as $podrazdelk => $podrazdelv) {
                $lk=0;
                $pr=0;
                $srs=0;
                $temy = '';
                $tema = 1;
                foreach($podrazdelv['temy'] as $temak=>$temav){
                    if ($temav['nazvanie']) {
                        $temy .= '<p>'.$razdel . '.' . $podrazdel . '.' . $tema . ' ' . $temav['nazvanie'] .
                            ' (' . ApiGlobals::first_letter_up($temav['tip_rabot']) . ', ' . $temav['chasy'] . ' ч.) - ' .
                            $temav['prepodavatel'].'</p>';
                        if ($temav['soderzhanie'])
                            $temy .= ApiGlobals::parse_text($temav['soderzhanie']);
                        if ($temav['forma_kontrolya_id']) {
                            $temy .= '<p>Контроль. ' . ApiGlobals::first_letter_up($temav['forma_kontrolya']) . '.</p>';
                        }
                        if ($temav['tip_rabot_id'] == 1) $lk += $temav['chasy'];
                        elseif (($temav['tip_rabot_id'] >= 2 and $temav['tip_rabot_id'] <= 10) or $temav['tip_rabot_id'] == 12) $pr += $temav['chasy'];
                        else $srs += $temav['chasy'];
                        $tema++;
                    }
                }
                $all+=$lk+$pr+$srs;
                $chasy = [];
                if ($lk) array_push($chasy,$lk.' л.');
                if ($pr) array_push($chasy,$pr.' пр.');
                if ($srs) array_push($chasy,$srs.' срс.');
                $podrazdely .= '<p><b>'.$razdel.'.'.$podrazdel.' '.$podrazdelv['nazvanie'].($chasy ? ' ('.join(', ',$chasy).')' : '').'</b></p>';
                $podrazdely .= $temy;
                $podrazdel++;
            }
            $razdely .= '<p><b>'.$k.' ('.$all.' ч.)</b></p>'.$podrazdely;
            $razdel++;
        }
        $result = $razdely;
        return $result;
    }

    public static function get_rukovoditel_podrazdeleniya($podrazdelenie_id){
        $sql = 'SELECT f.familiya||\' \'||f.imya||\' \'||f.otchestvo as fio,
                       f.familiya,f.imya,f.otchestvo
                FROM strukturnoe_podrazdelenie as sp
                INNER JOIN dolzhnost_fiz_lica_na_rabote as d on sp.id = d.strukturnoe_podrazdelenie
                INNER JOIN rabota_fiz_lica as rfl on d.rabota_fiz_lica = rfl.id
                INNER JOIN fiz_lico as f on rfl.fiz_lico = f.id
                WHERE d.rukovoditel_strukturnogo_podrazdeleniya and sp.id = :podrazdelenie';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':podrazdelenie',$podrazdelenie_id)->queryOne();
        if ($res) return $res;
        else return false;
    }

    public static function  add_razdel_nazvanie($nazvanie){
        $sql = 'INSERT INTO nazvanie_dlya_razdela_kursa (nazvanie) VALUES(:nazvanie)';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':nazvanie',$nazvanie)->execute();
        if ($res){
            return Yii::$app->db->getLastInsertID('nazvanie_dlya_razdela_kursa_id_seq');
        }
        else return false;
    }


    public static function get_hours_count_per_week($week_num,$podrazdel_id)
    {
        $sql = 'SELECT sum(t.chasy) as chasy FROM kurs as k
                inner join razdel_kursa as rk on k.id = rk.kurs
                inner join podrazdel_kursa as pk on rk.id = pk.razdel
                inner join tema as t on pk.id = t.podrazdel
                where k.id in
                 (
                    select kurs.id from kurs
                    inner join razdel_kursa on kurs.id = razdel_kursa.kurs
                    inner join podrazdel_kursa on razdel_kursa.id = podrazdel_kursa.razdel
                    where podrazdel_kursa.id = :podrazdel_id
                 ) and t.nedelya = :nedelya';
        $res = Yii::$app->db->createCommand($sql)
            ->bindValue(':podrazdel_id',$podrazdel_id)->bindValue(':nedelya',$week_num)
            ->queryScalar();
        if ($res) return $res;
        else return false;
    }
}
