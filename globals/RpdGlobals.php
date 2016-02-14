<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 07.06.15
 * Time: 19:01
 */

namespace app\globals;
use app\enums\StatusProgrammyKursa;
use Yii;
use yii\helpers\Url;


class RpdGlobals {

    public static function getSpisokDiscipline($rukovoditel){
        $sql = 'select k.nazvanie as kurs_nazvanie,
                pk.nazvanie as podrazdel_nazvanie,
                 pk.id as podrazdel_id
                from kurs as k
                inner join razdel_kursa as rk on k.id = rk.kurs
                inner join podrazdel_kursa as pk on rk.id = pk.razdel
                where k.tip != \'pk\' and pk.rukovoditel = :rukovoditel
                order by k.id,pk.nomer';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':rukovoditel',$rukovoditel)->queryAll();
        $spisok_discipline = [];
        if ($res){
            foreach ($res as $k=>$v) {
                $spisok_discipline[$v['kurs_nazvanie']][$v['podrazdel_id']] = $v['podrazdel_nazvanie'];
            }

        }
        return $spisok_discipline;
    }

    public static function get_podrazdel_and_themes($id){
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
                      kim_podrazdel.id as kim_pk_id,kim_podrazdel.opisanie as kim_pk_opisanie,kim_podrazdel.fajl as kim_pk_fajl,
                      kim_podrazdel.uri as kim_pk_uri, kim_podrazdel.text as kim_pk_text, kim_pk_f.vnutrennee_imya_fajla as kim_pk_file_disk_name,
                      kim_pk_f.vneshnee_imya_fajla as kim_pk_file_show_name,
                      umk_podrazdel.id as umk_pk_id,umk_podrazdel.opisanie as umk_pk_opisanie,umk_podrazdel.fajl as umk_pk_fajl,
                      umk_podrazdel.uri as umk_pk_uri, umk_pk_f.vnutrennee_imya_fajla as umk_pk_file_disk_name,
                      umk_pk_f.vneshnee_imya_fajla as umk_pk_file_show_name,
                      case when kontrol_pk.kontroliruyuschij_vakansiya then -1 else fz_kontrol.id end as kontrol_id,
                      case when kontrol_pk.kontroliruyuschij_vakansiya then \'Вакансия\'
                      else fz_kontrol.familiya||\' \'||fz_kontrol.imya||\' \'||fz_kontrol.otchestvo end as kontrol_fio,
                      rk.id as razdel_id, k.tip as tip_kursa,
                      rukovoditel_podrazdela.id as rukovoditel_podrazdela_id,
                      rukovoditel_podrazdela.familiya||\' \'||rukovoditel_podrazdela.imya||\' \'||rukovoditel_podrazdela.otchestvo as rukovoditel_podrazdela_fio
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
                  left join kontroliruyuschij_podrazdela_kursa as kontrol_pk on pk.id = kontrol_pk.podrazdel_kursa
                  left join fiz_lico as fz_kontrol on kontrol_pk.kontroliruyuschij_fiz_lico = fz_kontrol.id
                  inner join kurs as k on rk.kurs = k.id
                  left join fiz_lico as rukovoditel_podrazdela on pk.rukovoditel = rukovoditel_podrazdela.id
                WHERE pk.id=:id
                ORDER BY pk.nomer, t.nomer,umk.id,kim.id,kim_podrazdel.id';
        if  ($query = Yii::$app->db->createCommand($sql)->bindValue(':id',$id)->queryAll()) {
            foreach($query as $k=>$v){
                if ($v['id']) {
                    if (!isset($res[$v['id']])) {
                        $res[$v['id']] = array();
                        $res[$v['id']]['nazvanie'] = $v['nazvanie'];
                        $res[$v['id']]['id'] = $v['id'];
                        $res[$v['id']]['razdel_id'] = $v['razdel_id'];
                        $res[$v['id']]['nomer'] = $v['nomer'];
                        $res[$v['id']]['tip_kursa'] = $v['tip_kursa'];
                        $res[$v['id']]['kf_podrazdel_id'] = $v['kf_podrazdel_id'];
                        $res[$v['id']]['chasy_kf_podrazdela'] = $v['chasy_kf_podrazdela'];
                        $res[$v['id']]['kf_podrazdela_name'] = $v['kf_podrazdela_name'];
                        $res[$v['id']]['rukovoditel_podrazdela_id'] = $v['rukovoditel_podrazdela_id'];
                        $res[$v['id']]['rukovoditel_podrazdela_fio'] = $v['rukovoditel_podrazdela_fio'];
                        $res[$v['id']]['podrazdel_lk'] = $v['podrazdel_lk'];
                        $res[$v['id']]['podrazdel_pr'] = $v['podrazdel_pr'];
                        $res[$v['id']]['podrazdel_srs'] = $v['podrazdel_srs'];
                        $res[$v['id']]['podrazdel_kims'] = [];
                        $res[$v['id']]['podrazdel_umks'] = [];
                        $res[$v['id']]['kontrol_fiz_lica'] = [];

                    }
                    if ($v['kontrol_id'] and !isset($res[$v['id']]['kontrol_fiz_lica'][$v['kontrol_id']]))
                        $res[$v['id']]['kontrol_fiz_lica'][$v['kontrol_id']] = $v['kontrol_fio'];
                    if ($v['kim_pk_id'] and !isset($res[$v['id']]['podrazdel_kims'][$v['kim_pk_id']])) {
                        $res[$v['id']]['podrazdel_kims'][$v['kim_pk_id']] =
                            [
                                'kim_id' => $v['kim_pk_id'],
                                'kim_opisanie' => $v['kim_pk_opisanie'],
                                'kim_fajl' => $v['kim_pk_fajl'],
                                'kim_uri' => $v['kim_pk_uri'],
                                'kim_text' => $v['kim_pk_text'],
                                'kim_file_disk_name' => $v['kim_pk_file_disk_name'],
                                'kim_file_show_name' => $v['kim_pk_file_show_name'],
                                'tip'=>1,
                                'tip_kursa'=>$v['tip_kursa']
                            ];
                    }
                    if ($v['umk_pk_id'] and !isset($res[$v['id']]['podrazdel_umks'][$v['umk_pk_id']])) {
                        $res[$v['id']]['podrazdel_umks'][$v['umk_pk_id']] =
                            [
                                'umk_id' => $v['umk_pk_id'],
                                'umk_opisanie' => $v['umk_pk_opisanie'],
                                'umk_fajl' => $v['umk_pk_fajl'],
                                'umk_uri' => $v['umk_pk_uri'],
                                'umk_file_disk_name' => $v['umk_pk_file_disk_name'],
                                'umk_file_show_name' => $v['umk_pk_file_show_name'],
                                'tip'=>1,
                                'tip_kursa'=>$v['tip_kursa']
                            ];
                    }
                    if ($v['theme_id']) {
                        if (!isset($res[$v['id']]['themes'][$v['theme_id']]))
                            $res[$v['id']]['themes'][$v['theme_id']] = $v;
                        if (!isset($res[$v['id']]['themes'][$v['theme_id']]['umks']))
                            $res[$v['id']]['themes'][$v['theme_id']]['umks'] = [];
                        if ($v['umk_id'] and !isset($res[$v['id']]['themes'][$v['theme_id']]['umks'][$v['umk_id']]))
                            $res[$v['id']]['themes'][$v['theme_id']]['umks'][$v['umk_id']] = ['umk_id' => $v['umk_id'],
                                'umk_opisanie' => $v['umk_opisanie'],
                                'umk_fajl' => $v['umk_fajl'],
                                'umk_uri' => $v['umk_uri'],
                                'umk_file_disk_name' => $v['umk_file_disk_name'],
                                'umk_file_show_name' => $v['umk_file_show_name'],
                                'theme_id' => $v['theme_id'],
                                'tip'=>2,
                                'tip_kursa'=>$v['tip_kursa']
                            ];
                        if (!isset($res[$v['id']]['themes'][$v['theme_id']]['kims']))
                            $res[$v['id']]['themes'][$v['theme_id']]['kims'] = [];
                        if ($v['kim_id'] and !isset($res[$v['id']]['themes'][$v['theme_id']]['kims'][$v['kim_id']]))
                            $res[$v['id']]['themes'][$v['theme_id']]['kims'][$v['kim_id']] = [
                                'kim_id' => $v['kim_id'],
                                'kim_opisanie' => $v['kim_opisanie'],
                                'kim_fajl' => $v['kim_fajl'],
                                'kim_uri' => $v['kim_uri'],
                                'kim_text' => $v['kim_text'],
                                'kim_file_disk_name' => $v['kim_file_disk_name'],
                                'kim_file_show_name' => $v['kim_file_show_name'],
                                'tip'=>2,
                                'tip_kursa'=>$v['tip_kursa']
                            ];

                        //theme_forma_kontrolya_name
                    } else $res[$v['id']]['themes'] = array();
                }

            }
        }
        return $res;
    }


    public static function get_podrazdel_row($item, $status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full=true){
        $chasy_sum = $item['podrazdel_lk']+$item['podrazdel_pr']+$item['podrazdel_srs'];
        $html =  '<tr id="podrazdel'.$item['id'].'" class="podrazdel'.$item['razdel_id'].' atr podrazdel-row numbered">
                    <td class="action-td">
                        <div class="actions-control">
                        '.($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                           '<span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div class="action"><span class="slink"  onclick="add_theme('.$item['id'].')">Добавить тему</span></div>
                               <div class="action"><span class="slink"  onclick="add_podrazdel_umk('.$item['id'].')">Добавить УМК</span></div>
                               <div id="add_podrazdel_kf_action'.$item['id'].'" class="action '.($item['kf_podrazdel_id'] ? 'hidden' : '').'"><span onclick="add_podrazdel_fk('.$item['id'].')" class="slink">Добавить форму контроля</span></div>
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
                            <input id="rp'.$item['id'].'" type="hidden" value="'.$item['rukovoditel_podrazdela_id'].'">
                        </div>
                        <input type="hidden" value="'.$item['nomer'].'" class="podrazdel_nomer" id="podrazdel_nomer'.$item['id'].'"/>
                        <input type="hidden" value="'.$item['id'].'" class="podrazdel_id">
                        <input type="hidden" value="'.$item['kf_podrazdel_id'].'" id="kf_podrazdel'.$item['id'].'">

                    </td>
                    <td>
                        <input value="'.$item['tip_kursa'].'" type="hidden" id="kurs_type">
                    </td>
                </tr>';
        if ($is_full) {
            if (isset($item['themes'])) {
                foreach ($item['themes'] as $tk => $tv) {
                    $html .= RpdGlobals::get_theme_row($tv,$status);
                }
            }
        }
        $html .= '<tr class="section_footer section_footer_podrazdel" id="section_footer_podrazdel' . $item['id'] . '"><td colspan="3"></td></tr>';
        if ($is_full) {
            if (isset($item['kf_podrazdel_id'])) {
                $html .= RpdGlobals::get_kf_podrazdela_row($item,$status);
            }
            if (isset($item['podrazdel_umks'])){
                foreach($item['podrazdel_umks'] as $key=>$value){
                    $html.= RpdGlobals::get_umk_row($value,$status);
                }
            }
        }
        $html .= '<tr class="section_footer section_footer_podrazdel" id="section_footer_podrazdel_kf' . $item['id'] . '"><td colspan="3"></td></tr>';
        return $html;
    }

    public static function get_theme_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full=true){
        $html= '<tr id="theme'.$item['theme_id'].'" class="theme'.$item['id'].' atr theme-row numbered">
                    <td class="action-td">
                    '.($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div id="add_kf_block'.$item['theme_id'].'" class="action '.(isset($item['theme_forma_kontrolya_name']) ? 'hidden' : '').' "><span class="slink"  onclick="add_theme_control_form('.$item['theme_id'].')">Добавить форму контроля</span></div>
                               <div class="action"><span onclick="edit_them('.$item['theme_id'].')" class="slink">Редактировать тему</span></div>
                               <div class="action"><span onclick="delete_theme('.$item['theme_id'].')" class="slink">Удалить тему</span></div>
                            </div>
                        </div>' : '').
                    '</td>
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
                    '.($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="movers">
                            <span onclick="theme_up('.$item['theme_id'].','.$item['id'].')" class="inline-block mover_arrow" title="Переместить тему вверх">⬆</span><br>
                            <span onclick="theme_down('.$item['theme_id'].','.$item['id'].')" class="inline-block mover_arrow"  title="Переместить тему вниз">⬇</span>
                        </div>' : '').
                    '</td>
                </tr>';
        if ($is_full) {
            if (isset($item['theme_forma_kontrolya_name'])){
                $html.=RpdGlobals::get_kf_row($item,$status);
            }
            if (isset($item['umks'])) {
                foreach ($item['umks'] as $k => $v) {
                    $html .= RpdGlobals::get_umk_row($v,$status);
                }

            }
            $html .= '<tr class="section_footer section_footer_theme" id="section_footer_theme' . $item['theme_id'] . '"><td colspan="3"></td></tr>';
        }
        return $html;
    }

    public static function get_umk_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA,$is_full=true){
        $html = '<tr id="umk'.$item['umk_id'].'" class="atr umk-row">
                    <td class="action-td">
                    '.($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div class="action"><span class="slink"  onclick="edit_umk('.$item['umk_id'].')">Редактировать УМК</span></div>
                               <div class="action"><span class="slink" onclick="delete_umk('.$item['umk_id'].','.$item['tip'].')">Удалить УМК</span></div>
                            </div>
                        </div>' : '').
                    '</td>
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
        if ($status == StatusProgrammyKursa::REDAKTIRUETSYA)
            $html.='<div class="actions-control">
                               <span class="actions">действия</span>
                               <div class="action-list">
                                   <span class="subarrowed">действия</span>
                                   <div class="action"><span class="slink"  onclick="add_kim('.$item['theme_id'].')">Добавить КИМ</span></div>
                                   <div class="action"><span class="slink"  onclick="edit_kf('.$item['theme_id'].')">Редактировать</span></div>
                                   <div class="action"><span class="slink" onclick="delete_kf('.$item['theme_id'].')">Удалить</span></div>
                                </div>
                            </div>';
        $html .= '</td>';
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
                    $html .= RpdGlobals::get_kim_row($v,$status);
                }
            }
            $html.= '<tr class="section_footer" id="section_footer_kf'.$item['theme_id'].'"><td colspan="3"></td></tr>';
        }
        return $html;
    }

    public static function get_kim_row($item,$status = StatusProgrammyKursa::REDAKTIRUETSYA){
        $html = '<tr id="kim'.$item['kim_id'].'" class="atr kim-row">
                    <td class="action-td">
                    '.($status == StatusProgrammyKursa::REDAKTIRUETSYA ?
                        '<div class="actions-control">
                           <span class="actions">действия</span>
                           <div class="action-list">
                               <span class="subarrowed">действия</span>
                               <div class="action"><span class="slink"  onclick="edit_kim('.$item['kim_id'].')">Редактировать КИМ</span></div>
                               <div class="action"><span class="slink" onclick="delete_kim('.$item['kim_id'].','.$item['tip'].')">Удалить КИМ</span></div>
                            </div>
                        </div>' : '').
                    '</td>
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
                    $html .= RpdGlobals::get_kim_row($v,$status);
                }
            }
            $html.= '<tr class="section_footer" id="section_footer_kf_podrazdela'.$item['id'].'"><td colspan="3"></td></tr>';
        }
        return $html;
    }

    public static function set_discipline_status($id,$status){
        $sql = 'UPDATE podrazdel_kursa SET status = :status where id = :id';
        $res = Yii::$app->db->createCommand($sql)
                            ->bindValue(':status',$status)
                            ->bindValue(':id',$id)
                ->execute();
        return $res;
    }

    public static function check_podrazdel($id){
        $sql = 'SELECT
                  pk.id,
                  pk.raschitano_chasov_lekcyj as lk,
                  pk.raschitano_chasov_praktik as pr,
                  pk.raschitano_chasov_srs as srs,
                  SUM(case when t.tip_raboty = 1 then t.chasy else 0 end) as temy_lk,
                  SUM(case when t.tip_raboty between 2 and 10 or t.tip_raboty=12 then t.chasy else 0 end) as temy_pr,
                  SUM(case when t.tip_raboty=11 then t.chasy else 0 end) as temy_srs
                FROM podrazdel_kursa as pk
                left join tema as t on pk.id = t.podrazdel
                where pk.id = :id
                group by pk.id,pk.raschitano_chasov_lekcyj,pk.raschitano_chasov_srs,pk.raschitano_chasov_praktik';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$id)->queryOne();
        $result = '';
        if ($res){
            if ($res['lk']!=$res['temy_lk']) $result .= '<p>Количество лекционных часов должно равняться '.$res['lk'].'</p>';
            if ($res['pr']!=$res['temy_pr']) $result .= '<p>Количество практических часов должно равняться '.$res['pr'].'</p>';
            if ($res['srs']!=$res['temy_srs']) $result .= '<p>Количество часов СРС должно равняться '.$res['srs'].'</p>';
        }
        else $result = 'Ошибка в запросе';
        return $result;
    }

    public static function get_kurs_info_by_podrazdel_id ($podrazdel_id){
        $sql = 'select sp.nazvanie as podrazdelenie,k.tip as kurs_tip,k.nazvanie as kurs_nazvanie,
                      k.id as kurs_id,sp.sokrashennoe_nazvanie as podrazdelenie_sokrashennoe_nazvanie,
                      string_agg(ks.nazvanie,\', \') as kategorii,
                      k.rezhim_zanyatij, k.forma_obucheniya,
                      fl.familiya as rukovoditel_familiya,
                      fl.imya as rukovoditel_imya,
                      fl.otchestvo as rukovoditel_otchestvo,
                      k.status_programmy as kusr_status
                FROM kurs as k
                inner join razdel_kursa as rk on k.id = rk.kurs
                inner join podrazdel_kursa as pk on rk.id = pk.razdel
                inner join strukturnoe_podrazdelenie as sp on k.strukturnoe_podrazdelenie = sp.id
                inner join fiz_lico as fl on k.rukovoditel = fl.id
                LEFT JOIN kategoriya_slushatelya_kursa as ksk on k.id = ksk.kurs
                LEFT JOIN kategoriya_slushatelya as ks on ksk.kategoriya_slushatelya = ks.id
                where pk.id = :id
                group by sp.nazvanie,k.tip ,k.nazvanie,
                      k.id ,sp.sokrashennoe_nazvanie, k.rezhim_zanyatij, k.forma_obucheniya,
                      fl.imya,fl.familiya,fl.otchestvo
                limit 1';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$podrazdel_id)->queryOne();
        if ($res) return $res;
        else return false;
    }

    public static function get_rukovoditel_podrazdela ($id){
        $sql = 'SELECT CONCAT(fl.familiya,\' \'||fl.imya,\' \'||fl.otchestvo) as fio,
                      fl.familiya,
                      fl.imya,
                      fl.otchestvo,
                      d.nazvanie as dolzhnost
                FROM podrazdel_kursa as pk
                inner join fiz_lico as fl on pk.rukovoditel = fl.id
                inner join rabota_fiz_lica as rfl on fl.id = rfl.fiz_lico
                inner join dolzhnost_fiz_lica_na_rabote as dfl on rfl.id = dfl.rabota_fiz_lica
                inner join dolzhnost as d on dfl.dolzhnost = d.id
                where pk.id = :id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$id)->queryOne();
        return $res ? $res : false;
    }

    public static function get_rukovoditel_podrazdeleniya($podrazdel_id){
        $sql = 'SELECT f.familiya||\' \'||f.imya||\' \'||f.otchestvo as fio,
                       f.familiya,f.imya,f.otchestvo
                FROM strukturnoe_podrazdelenie as sp
                INNER JOIN dolzhnost_fiz_lica_na_rabote as d on sp.id = d.strukturnoe_podrazdelenie
                INNER JOIN rabota_fiz_lica as rfl on d.rabota_fiz_lica = rfl.id
                INNER JOIN fiz_lico as f on rfl.fiz_lico = f.id
                inner join kurs as k on k.rukovoditel = f.id
                inner join razdel_kursa as rk on k.id = rk.kurs
                inner join podrazdel_kursa as pk on rk.id = pk.razdel
                WHERE d.rukovoditel_strukturnogo_podrazdeleniya and pk.id = :podrazdel_id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':podrazdel_id',$podrazdel_id)->queryOne();
        if ($res) return $res;
        else return false;
    }

    public static function get_rpd_soderzhanie($podrazdel_id){
        $sql = 'SELECT
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
                       t.tip_raboty as tema_tip_rabot_id,
                       t.nomer as tema_nomer,
                       pk.nomer as podrazdel_nomer,
                       podrazdel_fk.nazvanie as forma_kontrolya_podrazdela,
                       pk.chasy_kontrolya as forma_kontrolya_podrazdela_chasy,
                       rukovoditel.familiya || \' \' || upper(substring(rukovoditel.imya  from 1 for 1)) || \'.\' || \' \' || upper(substring(rukovoditel.otchestvo from 1 for 1)) || \'.\' as rukovoditel
                FROM podrazdel_kursa as pk
                LEFT JOIN tema as t on pk.id = t.podrazdel
                LEFT JOIN rabota_po_teme as rpt on t.tip_raboty = rpt.id
                LEFT JOIN fiz_lico as fz on t.prepodavatel_fiz_lico = fz.id
                LEFT JOIN forma_kontrolya_v_techenie_kursa as fk on t.forma_kontrolya = fk.id
                LEFT JOIN forma_kontrolya_v_techenie_kursa as podrazdel_fk on pk.forma_kontrolya = podrazdel_fk.id
                LEFT JOIN fiz_lico as rukovoditel on pk.rukovoditel = rukovoditel.id
                WHERE pk.id = :id
                ORDER BY pk.nomer,t.nomer';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':id',$podrazdel_id)->queryAll();
        $s = [];
        foreach ($res as $k=>$v) {
            if (!isset($s[$v['podrazdel_id']])){
                $s[$v['podrazdel_id']] = ['nazvanie'=>$v['podrazdel_nazvanie'],
                    'nomer'=>$v['podrazdel_nomer'],
                    'forma_kontrolya_podrazdela'=>$v['forma_kontrolya_podrazdela'],
                    'forma_kontrolya_podrazdela_chasy' => $v['forma_kontrolya_podrazdela_chasy'],
                    'rukovoditel'=>$v['rukovoditel'],
                    'temy'=>[]];
            }
            if (!isset($s[$v['podrazdel_id']]['temy'][$v['tema_id']])){
                $s[$v['podrazdel_id']]['temy'][$v['tema_id']] = ['nazvanie'=>$v['tema_nazvanie'],
                    'soderzhanie'=>$v['tema_soderzhanie'],
                    'chasy'=>$v['tema_chasy'],
                    'prepodavatel'=>$v['prepodavatel'],
                    'forma_kontrolya'=>$v['tema_forma_kontrolya'],
                    'forma_kontrolya_id'=>$v['forma_kontrolya_id'],
                    'tip_rabot' => $v['tema_tip_rabot'],
                    'tip_rabot_id'=>$v['tema_tip_rabot_id'],
                    'nomer'=>$v['tema_nomer']
                ];
            }
        }
        return $s;
    }

    public static function get_rpd_soderzhanie_html($soderzhanie,$nomer=0)
    {
        $result = '';
        $lk = 0;
        $pr = 0;
        $srs = 0;
        $temy = '';
        $tema = 1;
        $podrazdel = current($soderzhanie);
        $podrazdel_html = '';
        foreach ($podrazdel['temy'] as $temak => $temav) {//$razdel.'.'.$podrazdel.'.'.$tema.' '.
            $temy .= '<p class="indent0">
                <i>'.$nomer.'.'.$podrazdel['nomer'].'.'.$temav['nomer'].' '.$temav['nazvanie'] .
                ' (' . ApiGlobals::first_letter_up($temav['tip_rabot']) . ', ' . $temav['chasy'] . ' ч.) - ' .
                $temav['prepodavatel'].'</i></p>'."\n";
            if ($temav['soderzhanie'])
                $temy .= '<p class="indent0">'.$temav['soderzhanie'].'</p>'."\n";
            if ($temav['forma_kontrolya_id']) {
                $temy .= '<p class="indent0">Контроль. ' . ApiGlobals::first_letter_up($temav['forma_kontrolya']) . '.</p>'."\n";
            }
        }
        $podrazdel_html .= '<p class="myp"><b>'.$nomer.'.'. $podrazdel['nomer'] .' '. $podrazdel['nazvanie'] . '</b></p>'."\n";//$razdel.'.'.$podrazdel.' '.
        $podrazdel_html .= $temy;
        $podrazdel_html .= '<p class="myp"><b>Итоговая аттестация по дисциплине. '.
            ApiGlobals::first_letter_up($podrazdel['forma_kontrolya_podrazdela']).' ('. $podrazdel['forma_kontrolya_podrazdela_chasy'] .'ч.) - '.
            $podrazdel['rukovoditel'].
            '</b></p>';
        $result = $podrazdel_html;
        return $result;
    }

    public static function get_nomer_razdela_v_kurse_by_podrazdel($kurs_id,$podrazdel_id){
        $sql = 'select razdel_nomer.nomer from
                  (
                    SELECT
                      row_number() OVER (ORDER BY rk.tip, rk.id) as nomer,
                      rk.id
                    FROM kurs AS k
                    INNER JOIN razdel_kursa AS rk ON k.id = rk.kurs
                    WHERE k.id = :kurs_id
                  ) as razdel_nomer
                  inner join podrazdel_kursa as pk on pk.razdel = razdel_nomer.id
                where pk.id = :podrazdel_id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':kurs_id',$kurs_id)->bindValue(':podrazdel_id',$podrazdel_id)->queryScalar();
        return $res;
    }

    public static function get_rpd_kims($podrazdel_id){
        $sql = 'select 1 as type,t.id as tema_id, t.nazvanie as tema,fkpt.nazvanie as forma_kontrolya,
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
                where pk.id = :id
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
                where pk.id = :id
                ';
        $kims = array();
        if ($res = Yii::$app->db->createCommand($sql)->bindValue(':id',$podrazdel_id)->queryAll()){
            $kims = $res;
        }
        return $kims;
    }

    public static function get_theme_number($tema_id,$soderzhanie){
        $result = '';
        $razdel = 1;
        foreach ($soderzhanie as $k=>$v) {
            $podrazdel = 1;
            foreach ($v as $podrazdelk => $podrazdelv) {
                $tema = 1;
                foreach($podrazdelv['temy'] as $temak=>$temav) {
                    if ($temak == $tema_id){
                        $result = $razdel.'.'.$podrazdel.'.'.$tema;
                        break;
                    }
                    $tema++;
                }
                if ($result) break;
                $podrazdel++;
            }
            if ($result) break;
            $razdel++;
        }
        return $result;
    }

    public static function get_rpd_kim_list_item($item,$soderzhanie=null){
        $content = '';
        if ($item['type']==1) {
            if ($soderzhanie)
                $content = '<p class="center">К теме ' .static::get_theme_number($item['tema_id'],$soderzhanie).' "'. $item['tema'] . '"</p>';
            else
                $content = '<p class="center">К теме "' . $item['tema'] . '"</p>';
        }
        if ($item['type'] == 3){
            if ($item['tip'] == 'pk')
                $content = '<p class="center">К блоку тему "' . $item['tema'] . '"</p>';
            else
                $content = '<p class="center">К дисциплине "' . $item['tema'] . '"</p>';
        }
        if ($item['type'] == 2)
            $content = '<p class="center">К итоговой аттестации</p>';
        $content .= '<p class="center"><b>'.ApiGlobals::first_letter_up($item['forma_kontrolya']).'</b></p>';

        if ($item['url']) $content.= ' - '.Html::a($item['url'],$item['url']);
        if ($item['file_url']) $content .= ' - '.Html::a($item['file_name'], Url::to(ApiGlobals::get_user_dir_url().$item['file_url']));
        if ($item['text']) $content .= ApiGlobals::parse_plain_text_to_html($item['text']);
        return $content;
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

    public static function get_kug_html($kug=[],$podrazdel_id,$min_week_num=0,$max_week_num = 0){
        $weekly_hours = [];//масси, в котром будет накапливаться недельные часы
        for($i=$min_week_num;$i<=$max_week_num;$i++) $weekly_hours[$i] = 0;
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
          '.($max_week_num ? KursGlobals::get_week_row(['begin'=>$min_week_num,'week_num'=>$max_week_num,'is_head'=>true]) : '').'
        </tr>
        </thead>
        <tbody>';

        $all_lk = 0;
        $all_pr = 0;
        $all_srs = 0;

//        $current_razdel_tip = '';

        $razdel_num=1;
        $is_break = false;

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
                    if ($pr == $podrazdel_id) {
                        $podrazdel_lk = 0;
                        $podrazdel_pr = 0;
                        $podrazdel_srs = 0;
                        $podrazdel_kontrol = [];
                        $tem_num = 1;
                        $temi = '';
                        //var_dump($pri);
                        //$first_elem = reset($pri['themes']);
                        foreach ($pri['themes'] as $tema_key => $tema_item) {
                            $week_hours = $tema_item['lk'] + $tema_item['pr'] + $tema_item['srs'];
                            $temi .= '<tr>
                            <td>' . $razdel_num . '.' . $podrazdel_num . '.' . $tem_num . '.</td>
                            <td>' . $tema_item['nazvanie'] . '</td>
                            <td class="center">' . ($week_hours ? $week_hours : '') . '</td>
                            <td class="center">' . ($tema_item['lk'] ? $tema_item['lk'] : '') . '</td>
                            <td class="center">' . ($tema_item['pr'] ? $tema_item['pr'] : '') . '</td>
                            <td class="center">' . ($tema_item['srs'] ? $tema_item['srs'] : '') . '</td>
                            <td class="center">' . (isset($tema_item['forma_kontrolya_temi']) ? $tema_item['forma_kontrolya_temi'] : '') . '</td>
                            ' . ($max_week_num ? KursGlobals::get_week_row(['begin'=>$min_week_num,'week_num' => $max_week_num, 'cur_week' => $tema_item['nedelya'], 'chasy' => $week_hours]) : '') . '
                        </tr>';
                            //var_dump($temi);
                            if (isset($tema_item['forma_kontrolya_temi'])) $podrazdel_kontrol[] = $tema_item['forma_kontrolya_temi'];
                            //if ($pri['razdel']=='var' and )
                            $podrazdel_lk += $tema_item['lk'];
                            $podrazdel_pr += $tema_item['pr'];
                            $podrazdel_srs += $tema_item['srs'];
                            if (($v['tip_razdela'] == 7 and $first_podrazdel == $pr) or $v['tip_razdela'] != 7) $weekly_hours[$tema_item['nedelya']] += $week_hours;
                            $tem_num++;
                        }
                        $podrzdel_kf = '';
                        if ($pri['podrazdel_fk_name']) {
                            $podrzdel_kf = '<tr class="sub2head">
                            <td></td>
                            <td>Итоговая аттестация по ' . ($v['tip_kursa'] == 'pk' ? 'блоку тем' : 'дисциплине') . '</td>
                            <td class="center">' . ($pri['podrazdel_chasy_fk'] ? $pri['podrazdel_chasy_fk'] : '') . '</td>
                            <td class="center"></td>
                            <td class="center">' . ($pri['podrazdel_chasy_fk'] ? $pri['podrazdel_chasy_fk'] : '') . '</td>
                            <td class="center"></td>
                            <td class="center">' . $pri['podrazdel_fk_name'] . '</td>
                            ' . ($max_week_num ? KursGlobals::get_week_row(['begin'=>$min_week_num,'week_num' => $max_week_num]) : '') . '
                        </tr>';
                            $podrazdel_pr += $pri['podrazdel_chasy_fk'];
                        }
                        $podrazdeli = '<tr class="sub2head">
                            <td>' . $razdel_num . '.' . $podrazdel_num . '.</td>
                            <td>' . $pri['nazvanie'] . '</td>
                            <td class="center">' . (($podrazdel_lk + $podrazdel_pr + $podrazdel_srs) ? ($podrazdel_lk + $podrazdel_pr + $podrazdel_srs) : '') . '</td>
                            <td class="center">' . ($podrazdel_lk ? $podrazdel_lk : '') . '</td>
                            <td class="center">' . ($podrazdel_pr ? $podrazdel_pr : '') . '</td>
                            <td class="center">' . ($podrazdel_srs ? $podrazdel_srs : '') . '</td>
                            <td class="center">' . implode(',', $podrazdel_kontrol) . '</td>
                            ' . ($max_week_num ? KursGlobals::get_week_row(['begin'=>$min_week_num,'week_num' => $max_week_num]) : '') . '
                        </tr>';
                        $podrazdeli_s_temami = $podrazdeli . $temi . $podrzdel_kf;
                        $for_kug .= $podrazdeli_s_temami;
                        if (($v['tip_razdela'] == 7 and $first_podrazdel == $pr) or $v['tip_razdela'] != 7) {
                            $razdel_lk += $podrazdel_lk;
                            $razdel_pr += $podrazdel_pr;
                            $razdel_srs += $podrazdel_srs;
                        }
                        $is_break = true;
                        break;
                    }
                    $podrazdel_num++;
                }
                if ($is_break) {
                    $razdels .= '<tr class="subhead razdel-tr">
                            <td></td>
                            <td>' . $v['nazvanie'] . '</td>
                            <td class="center"></td>
                            <td class="center"></td>
                            <td class="center"></td>
                            <td class="center"></td>
                            <td></td>
                            ' . ($max_week_num ? KursGlobals::get_week_row(['begin' => $min_week_num, 'week_num' => $max_week_num]) : '') . '
                        </tr>';
                    $razdels .= $for_kug;
                    $razdel_num++;
                    $all_lk += $razdel_lk;
                    $all_pr += $razdel_pr;
                    $all_srs += $razdel_srs;
                    break;
                }
            }
            if ($is_break) {
                $kug_tb .= '<tr>
                        <td class="center" colspan="7">' . KursGlobals::get_tip_razdela_name($tip_razdela) . '</td>
                        ' . ($max_week_num ? KursGlobals::get_week_row(['begin' => $min_week_num, 'week_num' => $max_week_num]) : '') . '
                       </tr>';
                $kug_tb .= $razdels;
                break;
            }
        }


        $kug_tb .= '<tr class="summary bold-tr">
                        <td></td>
                        <td>Итого</td>
                        <td class="center">'.(($all_lk+$all_pr+$all_srs) ? ($all_lk+$all_pr+$all_srs) : '').'</td>
                        <td class="center">'.($all_lk ? $all_lk : '').'</td>
                        <td class="center">'.($all_pr ? $all_pr : '').'</td>
                        <td class="center">'.($all_srs ? $all_srs : '').'</td>
                        <td></td>
                        '.($max_week_num ? KursGlobals::get_week_row(['begin'=>$min_week_num,'week_num'=>$max_week_num,'weekly_hours'=>$weekly_hours]) : '').'
                    </tr>';

        $kug_tb .= '</tbody></table>';

        return $kug_tb;
    }


    public static function get_uchebnii_plan_html($kug=[],$podrazdel_id){
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
        $is_break = false;
        foreach ($kug as $tip_razdela => $razdeli) {
            $razdels = '';
            foreach ($razdeli as $k => $v){

                $razdel_lk=0;
                $razdel_pr = 0;
                $razdel_srs = 0;

                $for_plan ='';
                $podrazdel_num =1;

                $first_podrazdel = key($v['podrazdels']);

                foreach ($v['podrazdels'] as $pr => $pri){
                    if ($pr == $podrazdel_id) {
                        $podrazdel_lk = 0;
                        $podrazdel_pr = 0;
                        $podrazdel_srs = 0;
                        $podrazdel_kontrol = [];
                        $tem_num = 1;
                        $temi = '';
                        foreach ($pri['themes'] as $tema_key => $tema_item) {
                            if (isset($tema_item['forma_kontrolya_temi'])) $podrazdel_kontrol[] = $tema_item['forma_kontrolya_temi'];
                            $podrazdel_lk += $tema_item['lk'];
                            $podrazdel_pr += $tema_item['pr'];
                            $podrazdel_srs += $tema_item['srs'];
                            $tem_num++;
                        }
                        $podrzdel_kf = '';
                        if ($pri['podrazdel_fk_name']) {
                            $podrzdel_kf = $pri['podrazdel_fk_name'] ? $pri['podrazdel_fk_name'] : '';
                            $podrazdel_pr += $pri['podrazdel_chasy_fk'];
                        }
                        $podrazdeli = '<tr class="sub2head">
                            <td>' . $razdel_num . '.' . $podrazdel_num . '.</td>
                            <td>' . $pri['nazvanie'] . '</td>
                            <td class="center">' . (($podrazdel_lk + $podrazdel_pr + $podrazdel_srs) ? $podrazdel_lk + $podrazdel_pr + $podrazdel_srs : '') . '</td>
                            <td class="center">' . ($podrazdel_lk ? $podrazdel_lk : '') . '</td>
                            <td class="center">' . ($podrazdel_pr ? $podrazdel_pr : '') . '</td>
                            <td class="center">' . ($podrazdel_srs ? $podrazdel_srs : '') . '</td>
                            <td class="center">' . $podrzdel_kf . '</td>
                        </tr>'; //implode(',', $podrazdel_kontrol)
                        $for_plan .= $podrazdeli;
                        if (($v['tip_razdela'] == 7 and $first_podrazdel == $pr) or $v['tip_razdela'] != 7) {
                            $razdel_lk += $podrazdel_lk;
                            $razdel_pr += $podrazdel_pr;
                            $razdel_srs += $podrazdel_srs;
                        }
                        $is_break = true;
                        break;
                    }
                    $podrazdel_num++;
                }
                if ($is_break) {

                    $razdels .= '<tr class="subhead  razdel-tr">
                            <td></td>
                            <td>' . $v['nazvanie'] . '</td>
                            <td class="center"></td>
                            <td class="center"></td>
                            <td class="center"></td>
                            <td class="center"></td>
                            <td></td>
                        </tr>';

                    $razdels .= $for_plan;
                    $razdel_num++;
                    $all_lk += $razdel_lk;
                    $all_pr += $razdel_pr;
                    $all_srs += $razdel_srs;
                    break;
                }
            }
            if ($is_break) {
                $plan .= '<tr>
                        <td class="center" colspan="7">' . KursGlobals::get_tip_razdela_name($tip_razdela) . '</td>
                     </tr>';
                $plan .= $razdels;
                break;
            }
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


    public static function get_max_min_weeks($podrazdel_id)
    {
        $sql = 'SELECT pk.id, min(t.nedelya) as min_nedelya, max(t.nedelya) as max_nedelya FROM podrazdel_kursa as pk
                inner join tema as t on pk.id = t.podrazdel
                where pk.id = :podrazdel_id
                group by pk.id';
        $res = Yii::$app->db->createCommand($sql)->bindValue(':podrazdel_id',$podrazdel_id)->queryOne();
        $result = [];
        if ($res){
            $result = ['min'=>$res['min_nedelya'],'max'=>$res['max_nedelya']];
        }
        return $result;
    }
}