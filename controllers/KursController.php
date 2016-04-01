<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 23.02.15
 * Time: 21:19
 */

namespace app\controllers;

use app\enums\StatusProgrammyKursa;
use app\models\attestatsiya\Kurs;
use app\models\Kurs\KursRecord;
use app\models\podrazdel_kursa\PodrazdelKursa;
use yii\base\Exception;
use yii\db\mssql\PDO;
use yii\db\Query;
use yii\web\Controller;
use \Yii;
use app\globals\ApiGlobals;
use app\globals\KursGlobals;
use app\globals\RpdGlobals;

class KursController extends Controller {

    public function beforeAction($action){
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionEdit()
    {
        //var_dump($_POST);
        $user = \Yii::$app->user;
        if ($user->isGuest)
            return $this->redirect('/polzovatel/vhod');

        $fiz_lico_id = ApiGlobals::getFizLicoPolzovatelyaId();
        $id = $_GET['id'];

        if (!$this->canEdit($fiz_lico_id, $id))
            return $this->redirect('/polzovatel/vhod');
        $kursModel = KursRecord::find($id)->with('kategoriyaSlushatelyas')->where(['id'=>$id])->one();
        if ($kursModel->load(Yii::$app->request->post())
            && $kursModel->validate())
        {
            KursGlobals::set_kurs_status($kursModel->id,'redaktiruetsya');
            $kursModel->save();
            $kursModel = KursRecord::find($id)->with('kategoriyaSlushatelyas')->where(['id'=>$id])->one();
        }
        if (!$kursModel->status_programmy) $kursModel->status_programmy = StatusProgrammyKursa::REDAKTIRUETSYA;
        $podrazdels = KursGlobals::get_podrazdel_and_themes($id);
        $attestaciya = KursGlobals::get_attestatciya($id);
        $vidy_rabot = KursGlobals::get_vidy_rabot();
        $sotrudniki = KursGlobals::get_sotrudniki();
        $sotrudniki[-1] = 'Вакансия';
        $kf_temi = KursGlobals::get_kontrolnie_formi_temi();
        if ($kursModel['tip'] != 'pk') $kf_temi[-1] = 'без формы контроля';
        $fiak = KursGlobals::get_formi_itogovoi_attestacii();
        $kug = KursGlobals::get_kug($id);
        $kims = KursGlobals::get_kims($id);
        $razdels = KursGlobals::get_razdels();
        $razdels[-1]='Другое';
        $max_week_num = KursGlobals::get_max_week_of_kurs($id);
        $razdel_types = ['baz'=>'Базовая часть','prof'=>'Профильная часть'];
        $weeks = [];
        for($i=1;$i<=40;$i++) $weeks[$i] = $i;
        return $this->render('edit',['kursModel'=>$kursModel,
                                     'podrazdels'=>$podrazdels,
                                     'vidy_rabot'=>$vidy_rabot,
                                     'sotrudniki'=>$sotrudniki,
                                     'kug' => $kug,
                                     'kf_temi' => $kf_temi,
                                     'fiak' => $fiak,
                                     'attestaciya' => $attestaciya,
                                     'kims' => $kims,
                                     'razdels' => $razdels,
                                     'max_week_num'=>$max_week_num,
                                     'weeks' => $weeks,
                                     'razdel_types'=>$razdel_types
                                    ]
                            );
    }

    public function actionRpd(){
        $rpd_id = $_GET['id'];
        $podrazdel = PodrazdelKursa::find()->where(['id'=>$rpd_id])->one();
        if (!$podrazdel) $podrazdel = new PodrazdelKursa();
        if ($podrazdel->load(Yii::$app->request->post())
            && $podrazdel->validate())
        {
            $podrazdel->save();
            $podrazdel = PodrazdelKursa::find()->where(['id'=>$rpd_id])->one();
        }
        $soderzhanie = RpdGlobals::get_podrazdel_and_themes($podrazdel['id']);
        $weeks = [];
        for($i=$podrazdel->nedelya_nachalo;$i<=$podrazdel->nedelya_konec;$i++) $weeks[$i] = $i;
        $vidy_rabot = KursGlobals::get_vidy_rabot();
        $sotrudniki = KursGlobals::get_sotrudniki();
        $sotrudniki[-1] = 'Вакансия';
        $kf_temi = KursGlobals::get_kontrolnie_formi_temi();
        $kf_temi[-1] = 'без формы контроля';
        $kurs_info = RpdGlobals::get_kurs_info_by_podrazdel_id($rpd_id);
        $nomer = RpdGlobals::get_nomer_razdela_v_kurse_by_podrazdel($kurs_info['kurs_id'],$rpd_id);
        return $this->render('redaktor-rpd',[
            'podrazdel'=>$podrazdel,
            'soderzhanie'=>$soderzhanie,
            'weeks'=>$weeks,
            'vidy_rabot'=>$vidy_rabot,
            'sotrudniki'=>$sotrudniki,
            'kf_temi'=>$kf_temi,
            'nomer' => $nomer,
            'kurs_info' => $kurs_info
        ]);
    }

    public function actionSpisokDiscipline(){
        $rukovoditel = ApiGlobals::getFizLicoPolzovatelyaId();
        $spisok_discipline = RpdGlobals::getSpisokDiscipline($rukovoditel);
        return $this->render('spisok-discipline',['spisok_discipline'=>$spisok_discipline]);
    }


    public function actionRpdAjax(){
        $ajax_query = $_POST['ajax_query'];
        $answer='';
        switch ($ajax_query) {
            case 'add_theme'://добавить тему
                $name = $_POST['name'];
                $podrazdel_id = $_POST['podrazdel_id'];
                $vid_rabot = $_POST['vid_rabot'];
                $sotrudnik = $_POST['sotrudnik'];
                $is_vakansiya = null;
                if ($sotrudnik == -1) {
                    $is_vakansiya = true;
                    $sotrudnik = null;
                }
                if (!$soderzhanie = $_POST['soderzhanie']) $soderzhanie = null;
                $name = ApiGlobals::to_trimmed_text($name);
                $soderzhanie = ApiGlobals::to_trimmed_text($soderzhanie);
                $chasy = $_POST['chasy'];
                $kurs_type = $_POST['kurs_type'];
                $nomer = $_POST['nomer'];
                $week = $_POST['week'];
                $sql = 'INSERT INTO tema (podrazdel,nazvanie,soderzhanie,tip_raboty,prepodavatel_fiz_lico,nomer,chasy,nedelya,prepodavatel_vakansiya)
                         VALUES (:podrazdel,:nazvanie,:soderzhanie,:tip_raboty,:prepodavatel,:nomer,:chasy,:nedelya,:vakansiya)';
                $errors ='';
                $week_hours = KursGlobals::get_hours_count_per_week($week,$podrazdel_id);
                if (!$name) $errors .= '<p>Введите название</p>';
                if (!$chasy) $errors .= '<p>Введите часы</p>';
                if ($week_hours < 54 and ($week_hours+$chasy) > 54) $errors .= '<p>Количество часов на одну неделю не должно превышать 54</p>';
                if ($kurs_type == 'pk' and $chasy > 4) $errors = '<p>Количество часов на одну тему курсов данного типа не должно превышать 4</p>';
                if (KursGlobals::is_podrazdel_var($podrazdel_id)){
                    $kurs_id = KursGlobals::get_kurs_by_podrazdel($podrazdel_id);
                    $first_podrazdel_count = KursGlobals::get_sum_hours_of_first_var_podrazdel($kurs_id);
                    $cur_podrazdel_hours = KursGlobals::get_sum_hours_of_podrazdel($podrazdel_id);
                    $cur_podrazdel_count = $cur_podrazdel_hours['chasy']+$chasy;
                    if ($cur_podrazdel_count>$first_podrazdel_count['hours'] and $first_podrazdel_count['podrazdel_id']!=$podrazdel_id) $errors.='<p>Количество часов в текущем блоке тем не должно быть больше количества часов первого блока тем вариативной части</p>';
                    if ($first_podrazdel_count['podrazdel_id']!=$podrazdel_id) {
                        if ($vid_rabot == 1 and $cur_podrazdel_hours['lk']+$chasy>$first_podrazdel_count['lk']) $errors .='<p>Количество часов на лекции в текущем блоке тем не должно быть больше количества часов на лекции первого блока тем вариативной части</p>';
                        if ($vid_rabot > 1 and $vid_rabot<=10 and $cur_podrazdel_hours['pr']+$chasy>$first_podrazdel_count['pr']) $errors .='<p>Количество часов на практики в текущем блоке тем не должно быть больше количества часов на практики первого блока тем вариативной части</p>';
                        if ($vid_rabot == 11 and $cur_podrazdel_hours['srs']+$chasy>$first_podrazdel_count['srs']) $errors .='<p>Количество часов на СРС в текущем блоке тем не должно быть больше количества часов на СРС первого блока тем вариативной части</p>';
                    }
                }
                if (!ApiGlobals::isEven($chasy)) $errors .= '<p>Количество часов должно быть кратно 2</p>';
                if (!$errors) {
                    if (Yii::$app->db->createCommand($sql)
                        ->bindValue(':podrazdel', $podrazdel_id)
                        ->bindValue(':nazvanie', $name)
                        ->bindValue(':soderzhanie', $soderzhanie)
                        ->bindValue(':tip_raboty', $vid_rabot)
                        ->bindValue(':prepodavatel', $sotrudnik)
                        ->bindValue(':nomer', $nomer)
                        ->bindValue(':chasy', $chasy)
                        ->bindValue(':nedelya', $week)
                        ->bindValue(':vakansiya',$is_vakansiya,PDO::PARAM_BOOL)
                        ->execute()
                    ) {
                        $answer['res'] = 'done';
                        $item = KursGlobals::get_theme_by_id(Yii::$app->db->getLastInsertID('tema_id_seq'));
                        $answer['html'] = RpdGlobals::get_theme_row($item);
                    } else {
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Тема не добавлена. Произошла ошибка во время выполнения запроса к базе данных';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = $errors;
                }
                break;
            case 'save_edit_theme': //редактирование темы
                $theme_id = $_POST['theme_id'];
                $nazvanie = $_POST['nazvanie'];
                if (!$soderzhanie = $_POST['soderzhanie']) $soderzhanie=null;
                $vid_rabot = $_POST['vid_rabot'];
                $prepodavatel = $_POST['prepodavatel'];
                $is_vakansiya = null;
                if ($prepodavatel == -1) {
                    $is_vakansiya = true;
                    $prepodavatel = null;
                }
                $chasy = $_POST['chasy'];
                $podrazdel_id = KursGlobals::get_podrazdel_by_tema($theme_id);
                $kurs_type = $_POST['kurs_type'];
                $week = $_POST['week'];
                $errors = '';
                $week_hours = KursGlobals::get_hours_count_per_week($week,$podrazdel_id);
                if (!$nazvanie) $errors .= '<p>Введите название</p>';
                if (!$chasy) $errors .= '<p>Введите часы</p>';
                if ($week_hours < 54 and ($week_hours+$chasy) > 54) $errors .= '<p>Количество часов на одну неделю не должно превышать 54</p>';
                if ($kurs_type == 'pk' and $chasy>4) $errors .= 'Количество часов на одну тему курсов данного типа не должно превышать 4';
                if (KursGlobals::is_podrazdel_var($podrazdel_id)){
                    $kurs_id = KursGlobals::get_kurs_by_podrazdel($podrazdel_id);
                    $first_podrazdel_count = KursGlobals::get_sum_hours_of_first_var_podrazdel($kurs_id);
                    $cur_podrazdel_hours = KursGlobals::get_sum_hours_of_podrazdel($podrazdel_id,$theme_id);
                    $cur_podrazdel_count = $cur_podrazdel_hours['chasy']+$chasy;
                    if ($cur_podrazdel_count>$first_podrazdel_count['hours'] and $first_podrazdel_count['podrazdel_id']!=$podrazdel_id) $errors.='Количество часов в текущем блоке тем не должно быть больше количества часов первого блока тем вариативной части';
                    if ($first_podrazdel_count['podrazdel_id']!=$podrazdel_id) {
                        if ($vid_rabot == 1 and $cur_podrazdel_hours['lk']+$chasy>$first_podrazdel_count['lk']) $errors .='<p>Количество часов на лекции в текущем блоке тем не должно быть больше количества часов на лекции первого блока тем вариативной части</p>';
                        if ($vid_rabot > 1 and $vid_rabot<=10 and $cur_podrazdel_hours['pr']+$chasy>$first_podrazdel_count['pr']) $errors .='<p>Количество часов на практики в текущем блоке тем не должно быть больше количества часов на практики первого блока тем вариативной части</p>';
                        if ($vid_rabot == 11 and $cur_podrazdel_hours['srs']+$chasy>$first_podrazdel_count['srs']) $errors .='<p>Количество часов на СРС в текущем блоке тем не должно быть больше количества часов на СРС первого блока тем вариативной части</p>';
                    }
                }
                if ($errors) {
                    $answer['res'] = 'error';
                    $answer['msg'] = $errors;
                }
                elseif ($nazvanie){
                    if (ApiGlobals::isEven($chasy)) {
                        $nazvanie = ApiGlobals::to_trimmed_text($nazvanie);
                        $soderzhanie = ApiGlobals::to_trimmed_text($soderzhanie);
                        $sql = 'UPDATE tema
                               set nazvanie = :nazvanie,
                                   soderzhanie = :soderzhanie,
                                   tip_raboty = :tip_raboty,
                                   prepodavatel_fiz_lico = :prepodavatel,
                                   chasy = :chasy,
                                   nedelya = :nedelya,
                                   prepodavatel_vakansiya = :vakansiya
                               where id = :id
                              ';
                        if (Yii::$app->db->createCommand($sql)
                            ->bindValue(':nazvanie', $nazvanie)
                            ->bindValue(':soderzhanie', $soderzhanie)
                            ->bindValue(':tip_raboty', $vid_rabot)
                            ->bindValue(':prepodavatel', $prepodavatel)
                            ->bindValue(':chasy', $chasy)
                            ->bindValue(':id', $theme_id)
                            ->bindValue(':nedelya',$week)
                            ->bindValue(':vakansiya',$is_vakansiya,PDO::PARAM_BOOL)
                            ->execute()
                        ) {
                            $answer['res'] = 'done';
                            $item = KursGlobals::get_theme_by_id($theme_id);
                            $answer['html'] = RpdGlobals::get_theme_row($item, StatusProgrammyKursa::REDAKTIRUETSYA, false);
                        } else {
                            $answer['res'] = 'error';
                            $answer['msg'] = 'Ошибка! Тема не обновлена. Ошибка в запросе к базе данных';
                        }
                    }
                    else{
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Количество часов должно быть кратно 2';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Введите название';
                }
                break;
            case 'delete_theme'://удалить тему
                $theme_id = $_POST['theme_id'];
                $is_have_umk_or_cc = KursGlobals::is_theme_have_umk_or_cc($theme_id);
                if (!$is_have_umk_or_cc) {
                    $sql = 'DELETE FROM tema where id = :id';
                    if (Yii::$app->db->createCommand($sql)->bindValue(':id', $theme_id)->execute()) {
                        $answer['res'] = 'done';
                    } else {
                        $answer['res'] = 'error';
                        $answer['type'] = 'danger';
                        $answer['msg'] = 'Ошибка выполнения запроса, тема не удалена!';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['type'] = 'warning';
                    $answer['msg'] = 'Тема не удалена! Сначала удалите все УМК и форму контроля';
                }
                break;
            case 'save_kf'://Добавить форму котроля
                $theme_id = $_POST['theme_id'];
                $forma_kf_id = $_POST['forma_kf_id'];
                $sql = 'UPDATE tema set forma_kontrolya = :forma_kontrolya where id=:id';
                if (Yii::$app->db->createCommand($sql)
                    ->bindValue(':forma_kontrolya',$forma_kf_id)
                    ->bindValue(':id',$theme_id)
                    ->execute()
                ){
                    $answer['res'] = 'done';
                    $kf =  KursGlobals::get_kf_by_theme_id($theme_id);
                    $answer['html'] = RpdGlobals::get_kf_row($kf);
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Контрольная форма не добавлена!';
                }
                break;
            case 'save_edit_kf'://редактировать форму котроля
                $theme_id = $_POST['theme_id'];
                $kf_id = $_POST['kf_id'];
                $sql = 'UPDATE tema set forma_kontrolya = :kf where id = :id';
                if (Yii::$app->db->createCommand($sql)
                    ->bindValue(':kf',$kf_id)
                    ->bindValue(':id',$theme_id)
                    ->execute()
                ){
                    $answer['res'] = 'done';
                    $kf = KursGlobals::get_kf_by_theme_id($theme_id);
                    $answer['html'] = RpdGlobals::get_kf_row($kf,StatusProgrammyKursa::REDAKTIRUETSYA,false);
                }
                else $answer['res'] = 'error';
                break;
            case 'delete_kf'://удалить контрольную форму
                $theme_id = $_POST['theme_id'];
                $is_have_kim = KursGlobals::is_kf_have_kim($theme_id);
                if (!$is_have_kim) {
                    $sql = 'UPDATE tema set forma_kontrolya = null where id = :id';
                    if (Yii::$app->db->createCommand($sql)->bindValue(':id', $theme_id)->execute()) {
                        $answer['res'] = 'done';
                    } else {
                        $answer['res'] = 'error';
                        $answer['type'] = 'danger';
                        $answer['msg'] = 'Ошибка выполнения запроса, тема не удалена!';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['type'] = 'warning';
                    $answer['msg'] = 'Форма контроля не удалена! Сначала удалите КИМы.';
                }
                break;
            case 'save_kim'://сохранить КИМ
                $theme_id = $_POST['theme_id'];
                $tip = $_POST['tip'];
                $is_error = false;
                $kim_id = KursGlobals::insert_kim($_POST);
                if ($kim_id){
                    $sql = 'INSERT INTO kim_temy (tema,kim) VALUES(:tema,:kim)';
                    $res = Yii::$app->db->createCommand($sql)
                        ->bindValue(':tema',$theme_id)
                        ->bindValue(':kim',$kim_id)
                        ->execute();
                    if (!$res) $is_error = true;
                }
                else $is_error=true;
                if (!$is_error){
                    $answer['res'] = 'done';
                    $kim = KursGlobals::get_kim_by_id($kim_id);
                    $kim['tip'] = $tip;
                    $answer['html'] = RpdGlobals::get_kim_row($kim);
                }
                else $answer['res'] = 'error';

                break;
            case 'delete_kim'://удалить КИМ
                $kim_id = $_POST['kim_id'];
                $tip = $_POST['tip'];
                switch($tip){
                    case 1:
                        $sql = 'DELETE FROM kim_podrazdela_kursa WHERE kim = :kim';
                        break;
                    case 2:
                        $sql = 'DELETE FROM kim_temy WHERE kim = :kim';
                        break;
                    case 3:
                        $sql = 'DELETE FROM kim_kursa WHERE kim = :kim';
                        break;
                }

                $res = Yii::$app->db->createCommand($sql)->bindValue(':kim',$kim_id)->execute();
                $is_error =false;
                if ($res){
                    if (!KursGlobals::delete_kim($kim_id)) $is_error = true;
                }
                else $is_error = true;
                if (!$is_error){
                    $answer['res'] = 'done';
                }
                else $answer['res'] = 'error';
                break;
            case 'save_edit_kim': //Редактировать КИМ
                $kim_id = $_POST['kim_id'];
                $tip = $_POST['tip'];
                if (KursGlobals::update_kim($_POST)){
                    $answer['res'] = 'done';
                    $kim = KursGlobals::get_kim_by_id($kim_id);
                    $kim['tip'] = $tip;
                    $answer['html'] = RpdGlobals::get_kim_row($kim);
                }
                else {
                    $answer['res'] = 'error';
                }
                break;
            case 'add_podrazdel_umk':
                $podrazdel_id = $_POST['podrazdel_id'];
                $umk_type = $_POST['umk_type'];
                $file = $_POST['file'];
                $url = $_POST['url'];
                $opisanie = $_POST['opisanie'];
                $tip = 1;
                if (($umk_type==1 and !$file) or ($umk_type==2 and !$url)){
                    $answer['res']='nothing';
                }
                else{
                    $umk_id = KursGlobals::insert_umk($_POST);
                    if ($umk_id){
                        $sql = 'INSERT INTO umk_podrazdela_kursa (podrazdel_kursa, umk) VALUES (:pk,:umk)';
                        $res = Yii::$app->db->createCommand($sql)->bindValue(':pk',$podrazdel_id)->bindValue(':umk',$umk_id)->execute();
                        if ($res){
                            $answer['res']='done';
                            $umk_item = KursGlobals::get_umk_by_id($umk_id);
                            $umk_item['tip'] = 1;
                            $answer['html'] = RpdGlobals::get_umk_row($umk_item);
                        }
                        else{
                            $answer['res']='error';
                            $answer['type']='danger';
                            $answer['msg'] = 'УМК не добален! Ошибка запроса к базе данных!';
                        }
                    }
                    else{
                        $answer['res']='error';
                        $answer['type']='danger';
                        $answer['msg'] = 'УМК не добален! Ошибка запроса к базе данных!';
                    }

                }
                break;
            case 'edit_umk'://Редактировать УМК
                $umk_id = $_POST['umk_id'];
                $umk_type = $_POST['umk_type'];
                $file = $_POST['file'];
                $url = $_POST['url'];
                $tip = $_POST['tip'];
                if (($umk_type==1 and !$file) or ($umk_type==2 and !$url)){
                    $answer['res']='nothing';
                }
                else{
                    $res = KursGlobals::update_umk($_POST);
                    if ($res){
                        $answer['res']='done';
                        $umk_item = KursGlobals::get_umk_by_id($umk_id);
                        $umk_item['tip'] = $tip;
                        $answer['html'] = RpdGlobals::get_umk_row($umk_item,StatusProgrammyKursa::REDAKTIRUETSYA,false);
                    }
                    else{
                        $answer['res']='error';
                        $answer['type']='danger';
                        $answer['msg'] = 'УМК не изменен! Ошибка запроса к базе данных!';
                    }

                }
                break;
            case 'delete_umk'://Удалить УМК
                $umk_id = $_POST['umk_id'];
                $is_error = false;
                $tip = $_POST['tip'];
                if ($tip==1)
                    $sql = 'DELETE FROM umk_podrazdela_kursa WHERE umk = :umk ';
                else
                    $sql = 'DELETE FROM umk_temy WHERE umk = :umk ';
                $res = Yii::$app->db->createCommand($sql)->bindValue(':umk',$umk_id)->execute();
                if ($res){
                    if (!KursGlobals::delete_umk($umk_id)) $is_error = true;
                }
                else $is_error = true;
                if (!$is_error){
                    $answer['res'] = 'done';
                }
                else{
                    $answer['res']='error';
                    $answer['type']='danger';
                    $answer['msg'] = 'УМК не удален! Ошибка запроса к базе данных!';
                }
                break;
            case 'save_podrazdel_kim':
                $podrazdel_id = $_POST['podrazdel_id'];
                $tip_kursa = $_POST['tip_kursa'];
                $tip = $_POST['tip'];
                $is_error = false;
                $kim_id = KursGlobals::insert_kim($_POST);
                if ($kim_id){
                    $sql = 'INSERT INTO kim_podrazdela_kursa (podrazdel_kursa,kim) VALUES(:pk,:kim)';
                    $res = Yii::$app->db->createCommand($sql)
                        ->bindValue(':pk',$podrazdel_id)
                        ->bindValue(':kim',$kim_id)
                        ->execute();
                    if (!$res) $is_error = true;
                }
                else $is_error=true;
                if (!$is_error){
                    $answer['res'] = 'done';
                    $kim =KursGlobals::get_kim_by_id($kim_id);
                    $kim['tip_kursa'] = $tip_kursa;
                    $kim['tip'] = $tip;
                    $answer['html'] = RpdGlobals::get_kim_row($kim);
                }
                else $answer['res'] = 'error';
            break;
            case 'sign_discipline':
                $id = $_POST['id'];
                $is_checked = $_POST['is_checked'];
                $is_error = false;
                $answer['is_set_podpis'] = 1;
                if ($is_checked){
                    $errors = '';
                    if ($errors = RpdGlobals::check_podrazdel($id)){
                        $answer['res'] = 'error';
                        $answer['msg'] = $errors;
                    }
                    else{
                        $res = RpdGlobals::set_discipline_status($id,1);
                        if ($res){
                             $answer['res'] = 'done';
                         }
                         else{
                             $answer['res'] = 'error';
                             $answer['msg'] = 'Ошибка выполнения запроса к базе данных. Подпись не сохранена';
                         }
                    }

                }
                else{
                    //$sql = 'UPDATE kurs SET status_programmy = \'redaktiruetsya\' where id = :id';
                    $res = RpdGlobals::set_discipline_status($id,0);
                    if ($res){
                        $answer['res'] = 'done';
                    }
                    else{
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Ошибка выполнения запроса к базе данных. Подпись не сохранена';
                    }
                }
                break;
            case 'save_theme_num_order':
                $order = $_POST['order'];
                if ($order) {
                    $t = Yii::$app->db->beginTransaction();
                    $is_error = false;
                    foreach ($order as $k => $v) {
                        $sql = 'UPDATE tema SET nomer = :nomer WHERE id = :id';
                        $res = Yii::$app->db->createCommand($sql)
                            ->bindValue(':nomer', $v['new'])
                            ->bindValue(':id', $k)
                            ->execute();
                        if (!$res){
                            $is_error = true;
                            break;
                        }
                    }
                    if (!$is_error){
                        $t->commit();
                        $answer['res'] = 'done';
                    }
                    else{
                        $t->rollBack();
                        $answer['res'] = 'error';
                        $answer['type'] = 'error';
                        $answer['msg'] = 'Во время сохрарения произошла ошибка. Данные не изменены.ƒ';
                    }
                }

                break;
        }
        return json_encode($answer);
    }

    public function actionAjax(){
        $ajax_query = $_POST['ajax_query'];
        $answer='';
        switch ($ajax_query) {
            case 'add_razdel':
                $kurs_id = $_POST['kurs_id'];
                $nazvanie = $_POST['nazvanie'];
                $type = $_POST['type'];
                $new_nazvanie = $_POST['new_nazvanie'];
                $is_error = false;
                if ($nazvanie == -1){
                    if (!$nazvanie = KursGlobals::add_razdel_nazvanie($new_nazvanie)) $is_error = true;
                }
                if ($is_error){
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Раздел не добавлен. Ошибка при добавлении нового названия';
                }
                elseif (!KursGlobals::is_razdel_already_added($kurs_id,$nazvanie)){
                    $sql = 'INSERT INTO razdel_kursa (kurs, nomer, nazvanie,tip) VALUES(:kurs,1,:nazvanie,:tip)';
                    $res = Yii::$app->db->createCommand($sql)->bindValue(':kurs',$kurs_id)
                                                             ->bindValue(':nazvanie',$nazvanie)
                                                             ->bindValue(':tip',$type)
                            ->execute();
                    if ($res){
                        $razdel_id = Yii::$app->db->getLastInsertID('razdel_kursa_id_seq');
                        $razdel = KursGlobals::get_razdel_by_id($razdel_id);
                        $answer['html'] = KursGlobals::get_razdel_row($razdel);
                        $answer['nazvanie'] = $nazvanie;
                        $answer['res'] = 'done';
                    }
                    else{
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Ошибка выполнения запроса к базе данных';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Данный раздел уже добавлен в содержание';
                }
            break;
            case 'edit_razdel':
                $razdel_id = $_POST['razdel_id'];
                $nazvanie_id = $_POST['nazvanie_id'];
                $old_nazvanie_id = $_POST['old_nazvanie_id'];
                $kurs_id = $_POST['kurs_id'];
                $type = $_POST['type'];
                $new_nazvanie = $_POST['new_nazvanie'];
                $is_error = false;
                if ($nazvanie_id == -1){
                    if (!$nazvanie_id = KursGlobals::add_razdel_nazvanie($new_nazvanie)) $is_error = true;
                }
                if ($is_error){
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Раздел не изменен. Ошибка при добавлении нового названия';
                }
                if (!KursGlobals::is_razdel_already_added($kurs_id,$nazvanie_id) or $old_nazvanie_id==$nazvanie_id) {
                    $sql = 'UPDATE razdel_kursa SET nazvanie = :nazvanie, tip = :tip where id = :id';
                    $res = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':nazvanie',$nazvanie_id)
                                        ->bindValue(':id',$razdel_id)
                                        ->bindValue(':tip',$type)
                        ->execute();
                    if ($res){
                        $razdel = KursGlobals::get_razdel_by_id($razdel_id);
                        $answer['html'] = KursGlobals::get_razdel_row($razdel,StatusProgrammyKursa::REDAKTIRUETSYA,false);
                        $answer['nazvanie'] = $nazvanie_id;
                        $answer['res'] = 'done';
                    }
                    else{
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Ошибка выполнения запроса к базе данных';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Данный раздел уже добавлен в содержание';
                }
            break;
            case 'delete_razdel':
                $razdel_id = $_POST['razdel_id'];
                if (!KursGlobals::is_razdel_have_podrazdels($razdel_id)){
                    $sql = 'DELETE FROM razdel_kursa WHERE id = :id';
                    $res  = Yii::$app->db->createCommand($sql)->bindValue(':id',$razdel_id)->execute();
                    if ($res){
                        $answer['res'] = 'done';
                    }
                    else{
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Раздел не удален. Произошла ошибка при выполнении запроса к базе даных.';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Раздел не удален. Сначала удалите все подразделы данного раздела.';
                }
            break;
            case 'add_podrazdel'://добавить подраздел
                $razdel = $_POST['razdel'];
                $name = $_POST['name'];
                $name = ApiGlobals::to_trimmed_text($name);
                $nomer = $_POST['nomer'];
                $rukovoditel = isset($_POST['rukovoditel']) ? $_POST['rukovoditel'] : null;
                $is_vakansiya = null;
                if ($rukovoditel == -1){
                    $rukovoditel = null;
                    $is_vakansiya = true;
                }
                $lk = isset($_POST['lk']) ? $_POST['lk'] : null;
                if (isset($_POST['pr']) and $_POST['pr']) $pr = $_POST['pr'];
                else $pr = null;
                if (isset($_POST['srs']) and $_POST['srs']) $srs = $_POST['srs'];
                else $srs = null;
                if (isset($_POST['fk']) and $_POST['fk']) $fk = $_POST['fk'];
                else $fk = null;
                $nedelya_nachalo = isset($_POST['nedelya_nachalo']) ? $_POST['nedelya_nachalo'] : null;
                $nedelya_konec = isset($_POST['nedelya_konec']) ? $_POST['nedelya_konec'] : null;
                $chasy_kontrolya = isset($_POST['chasy_kontrolya']) ? $_POST['chasy_kontrolya'] : null;
                if (!$chasy_kontrolya) $chasy_kontrolya = null;
                if ($fk == -1) $fk = null;
                $kurs_type = $_POST['kurs_type'];
                $sql = 'INSERT INTO podrazdel_kursa
                        (nazvanie, razdel,nomer,rukovoditel,raschitano_chasov_lekcyj,
                         raschitano_chasov_praktik,raschitano_chasov_srs,forma_kontrolya,
                         nedelya_nachalo,nedelya_konec,rukovoditel_vakansiya,chasy_kontrolya)
                        VALUES (:nazvanie, :razdel,:nomer,:rukovoditel,:lk,
                                :pr,:srs,:fk,:nedelya_nachalo,:nedelya_konec,:is_vakansiya,
                                :chasy_kontrolya)';
                if (Yii::$app->db->createCommand($sql)
                             ->bindValue(':nazvanie',$name)
                             ->bindValue(':razdel',$razdel)
                             ->bindValue(':nomer',$nomer)
                             ->bindValue(':rukovoditel',$rukovoditel)
                             ->bindValue(':lk',$lk)
                             ->bindValue(':pr',$pr)
                             ->bindValue(':srs',$srs)
                             ->bindValue(':fk',$fk)
                             ->bindValue(':nedelya_nachalo',$nedelya_nachalo)
                             ->bindValue(':nedelya_konec',$nedelya_konec)
                             ->bindValue(':is_vakansiya',$is_vakansiya)
                             ->bindValue(':chasy_kontrolya',$chasy_kontrolya)
                    ->execute()){
                    $id = Yii::$app->db->getLastInsertID('podrazdel_kursa_id_seq');
                    $podrazdel = KursGlobals::get_podrazdel_by_id($id);//['id'=>$id,'nazvanie'=>$name,'kf_podrazdel_id'=>null];
                    //$podrazdel['kf_podrazdel_id'] = null;
                    $answer['html'] =  KursGlobals::get_podrazdel_row($podrazdel,StatusProgrammyKursa::REDAKTIRUETSYA,false);
                    $answer['res']='done';
                    $answer['df'] = $podrazdel;
                }
                else{
                    $answer['res'] = 'error';
                }
            break;
            case 'edit_podrazdel'://сохранить изменения подраздела
                $name = $_POST['name'];
                $podrazdel_id = $_POST['podrazdel_id'];
                $rukovoditel = isset($_POST['rukovoditel']) ? $_POST['rukovoditel'] : null;
                $is_vakansiya = null;
                if ($rukovoditel == -1) {
                    $rukovoditel = null;
                    $is_vakansiya = true;
                }
                $lk = isset($_POST['lk']) ? $_POST['lk'] : null;
                if (isset($_POST['pr']) and $_POST['pr']) $pr = $_POST['pr'];
                else $pr = null;
                if (isset($_POST['srs']) and $_POST['srs']) $srs = $_POST['srs'];
                else $srs = null;
                if (isset($_POST['fk']) and $_POST['fk']) $fk = $_POST['fk'];
                else $fk = null;
                $nedelya_nachalo = isset($_POST['nedelya_nachalo']) ? $_POST['nedelya_nachalo'] : null;
                $nedelya_konec = isset($_POST['nedelya_konec']) ? $_POST['nedelya_konec'] : null;
                $chasy_kontrolya = isset($_POST['chasy_kontrolya']) ? $_POST['chasy_kontrolya'] : null;
                if (!$chasy_kontrolya) $chasy_kontrolya = null;
                if ($fk == -1) $fk = null;
                $sql = 'UPDATE podrazdel_kursa set nazvanie=:nazvanie, rukovoditel=:rukovoditel,
                                raschitano_chasov_lekcyj = :lk, raschitano_chasov_praktik = :pr,
                                raschitano_chasov_srs = :srs, forma_kontrolya = :fk,
                                nedelya_nachalo = :nedelya_nachalo,
                                nedelya_konec = :nedelya_konec,
                                rukovoditel_vakansiya = :is_vakansiya,
                                chasy_kontrolya = :chasy_kontrolya
                        where id = :id';
                if (Yii::$app->db->createCommand($sql)
                                 ->bindValue(':nazvanie',$name)
                                 ->bindValue(':rukovoditel',$rukovoditel)
                                 ->bindValue(':lk',$lk)
                                 ->bindValue(':pr',$pr)
                                 ->bindValue(':srs',$srs)
                                 ->bindValue(':fk',$fk)
                                 ->bindValue(':nedelya_nachalo',$nedelya_nachalo)
                                 ->bindValue(':nedelya_konec',$nedelya_konec)
                                 ->bindValue(':is_vakansiya',$is_vakansiya)
                                 ->bindValue(':chasy_kontrolya',$chasy_kontrolya)
                                 ->bindValue(':id',$podrazdel_id)
                    ->execute()){
                    $podrazdel = KursGlobals::get_podrazdel_by_id($podrazdel_id);
                    $answer['res'] = 'done';
                    $answer['html'] = KursGlobals::get_podrazdel_row($podrazdel,StatusProgrammyKursa::REDAKTIRUETSYA,false);
                }
                else $answer['res']='error';
            break;
            case 'delete_podrazdel'://удалить подраздел
                $podrazdel_id = $_POST['podrazdel_id'];
                if (!KursGlobals::is_podrazdel_have_themes($podrazdel_id)) {
                    $sql = 'DELETE FROM podrazdel_kursa where id=:id';
                    if (Yii::$app->db->createCommand($sql)->bindValue(':id', $podrazdel_id)->execute()) {
                        $answer['res'] = 'done';
                    } else {
                        $answer['res'] = 'error';
                        $answer['type'] = 'danger';
                        $answer['msg'] = 'Запрос выполнены с ошибкой';
                    }
                }
                else {
                    $answer['res'] = 'error';
                    $answer['type'] = 'warning';
                    $answer['msg'] = 'Подраздел не удален. Сначала удалите все темы';
                }
            break;
            case 'add_theme'://добавить тему
                 $name = $_POST['name'];
                 $podrazdel_id = $_POST['podrazdel_id'];
                 $vid_rabot = $_POST['vid_rabot'];
                 $sotrudnik = $_POST['sotrudnik'];
                 $is_vakansiya = null;
                 if ($sotrudnik == -1) {
                     $is_vakansiya = true;
                     $sotrudnik = null;
                 }
                 if (!$soderzhanie = $_POST['soderzhanie']) $soderzhanie = null;
                 $name = ApiGlobals::to_trimmed_text($name);
                 $soderzhanie = ApiGlobals::to_trimmed_text($soderzhanie);
                 $chasy = $_POST['chasy'];
                 $kurs_type = $_POST['kurs_type'];
                 $nomer = $_POST['nomer'];
                 $week = $_POST['week'];
                 $sql = 'INSERT INTO tema (podrazdel,nazvanie,soderzhanie,tip_raboty,prepodavatel_fiz_lico,nomer,chasy,nedelya,prepodavatel_vakansiya)
                         VALUES (:podrazdel,:nazvanie,:soderzhanie,:tip_raboty,:prepodavatel,:nomer,:chasy,:nedelya,:vakansiya)';
                 $errors ='';
                 $week_hours = KursGlobals::get_hours_count_per_week($week,$podrazdel_id);
                 if (!$name) $errors .= '<p>Введите название</p>';
                 if (!$chasy) $errors .= '<p>Введите часы</p>';
                 if ($week_hours < 54 and ($week_hours+$chasy) > 54) $errors .= '<p>Количество часов на одну неделю не должно превышать 54</p>';
                 if ($kurs_type == 'pk' and $chasy > 4) $errors = '<p>Количество часов на одну тему курсов данного типа не должно превышать 4</p>';
                 if (KursGlobals::is_podrazdel_var($podrazdel_id)){
                     $kurs_id = KursGlobals::get_kurs_by_podrazdel($podrazdel_id);
                     $first_podrazdel_count = KursGlobals::get_sum_hours_of_first_var_podrazdel($kurs_id);
                     $cur_podrazdel_hours = KursGlobals::get_sum_hours_of_podrazdel($podrazdel_id);
                     $cur_podrazdel_count = $cur_podrazdel_hours['chasy']+$chasy;
                     if ($cur_podrazdel_count>$first_podrazdel_count['hours'] and $first_podrazdel_count['podrazdel_id']!=$podrazdel_id) $errors.='<p>Количество часов в текущем блоке тем не должно быть больше количества часов первого блока тем вариативной части</p>';
                     if ($first_podrazdel_count['podrazdel_id']!=$podrazdel_id) {
                         if ($vid_rabot == 1 and $cur_podrazdel_hours['lk']+$chasy>$first_podrazdel_count['lk']) $errors .='<p>Количество часов на лекции в текущем блоке тем не должно быть больше количества часов на лекции первого блока тем вариативной части</p>';
                         if ($vid_rabot > 1 and $vid_rabot<=10 and $cur_podrazdel_hours['pr']+$chasy>$first_podrazdel_count['pr']) $errors .='<p>Количество часов на практики в текущем блоке тем не должно быть больше количества часов на практики первого блока тем вариативной части</p>';
                         if ($vid_rabot == 11 and $cur_podrazdel_hours['srs']+$chasy>$first_podrazdel_count['srs']) $errors .='<p>Количество часов на СРС в текущем блоке тем не должно быть больше количества часов на СРС первого блока тем вариативной части</p>';
                     }
                 }
                 if (!ApiGlobals::isEven($chasy)) $errors .= '<p>Количество часов должно быть кратно 2</p>';
                 if (!$errors) {
                     if (Yii::$app->db->createCommand($sql)
                         ->bindValue(':podrazdel', $podrazdel_id)
                         ->bindValue(':nazvanie', $name)
                         ->bindValue(':soderzhanie', $soderzhanie)
                         ->bindValue(':tip_raboty', $vid_rabot)
                         ->bindValue(':prepodavatel', $sotrudnik)
                         ->bindValue(':nomer', $nomer)
                         ->bindValue(':chasy', $chasy)
                         ->bindValue(':nedelya', $week)
                         ->bindValue(':vakansiya',$is_vakansiya,PDO::PARAM_BOOL)
                         ->execute()
                     ) {
                         $answer['res'] = 'done';
                         $item = KursGlobals::get_theme_by_id(Yii::$app->db->getLastInsertID('tema_id_seq'));
                         $answer['html'] = KursGlobals::get_theme_row($item);
                     } else {
                         $answer['res'] = 'error';
                         $answer['msg'] = 'Тема не добавлена. Произошла ошибка во время выполнения запроса к базе данных';
                     }
                 }
                 else{
                     $answer['res'] = 'error';
                     $answer['msg'] = $errors;
                 }
            break;
            case 'save_edit_theme': //редактирование темы
                  $theme_id = $_POST['theme_id'];
                  $nazvanie = $_POST['nazvanie'];
                  if (!$soderzhanie = $_POST['soderzhanie']) $soderzhanie=null;
                  $vid_rabot = $_POST['vid_rabot'];
                  $prepodavatel = $_POST['prepodavatel'];
                  $is_vakansiya = null;
                  if ($prepodavatel == -1) {
                      $is_vakansiya = true;
                      $prepodavatel = null;
                  }
                  $chasy = $_POST['chasy'];
                  $podrazdel_id = KursGlobals::get_podrazdel_by_tema($theme_id);
                  $kurs_type = $_POST['kurs_type'];
                  $week = $_POST['week'];
                  $week_hours = KursGlobals::get_hours_count_per_week($week,$podrazdel_id);
                  $errors = '';
                  if (!$nazvanie) $errors .= '<p>Введите название</p>';
                  if (!$chasy) $errors .= '<p>Введите часы</p>';
                  if ($week_hours < 54 and ($week_hours+$chasy) > 54) $errors .= '<p>Количество часов на одну неделю не должно превышать 54</p>';
                  if ($kurs_type == 'pk' and $chasy>4) $errors .= 'Количество часов на одну тему курсов данного типа не должно превышать 4';
                  if (KursGlobals::is_podrazdel_var($podrazdel_id)){
                      $kurs_id = KursGlobals::get_kurs_by_podrazdel($podrazdel_id);
                      $first_podrazdel_count = KursGlobals::get_sum_hours_of_first_var_podrazdel($kurs_id);
                      $cur_podrazdel_hours = KursGlobals::get_sum_hours_of_podrazdel($podrazdel_id,$theme_id);
                      $cur_podrazdel_count = $cur_podrazdel_hours['chasy']+$chasy;
                      if ($cur_podrazdel_count>$first_podrazdel_count['hours'] and $first_podrazdel_count['podrazdel_id']!=$podrazdel_id) $errors.='Количество часов в текущем блоке тем не должно быть больше количества часов первого блока тем вариативной части';
                      if ($first_podrazdel_count['podrazdel_id']!=$podrazdel_id) {
                          if ($vid_rabot == 1 and $cur_podrazdel_hours['lk']+$chasy>$first_podrazdel_count['lk']) $errors .='<p>Количество часов на лекции в текущем блоке тем не должно быть больше количества часов на лекции первого блока тем вариативной части</p>';
                          if ($vid_rabot > 1 and $vid_rabot<=10 and $cur_podrazdel_hours['pr']+$chasy>$first_podrazdel_count['pr']) $errors .='<p>Количество часов на практики в текущем блоке тем не должно быть больше количества часов на практики первого блока тем вариативной части</p>';
                          if ($vid_rabot == 11 and $cur_podrazdel_hours['srs']+$chasy>$first_podrazdel_count['srs']) $errors .='<p>Количество часов на СРС в текущем блоке тем не должно быть больше количества часов на СРС первого блока тем вариативной части</p>';
                      }
                  }
                  if ($errors) {
                      $answer['res'] = 'error';
                      $answer['msg'] = $errors;
                  }
                  elseif ($nazvanie){
                      if (ApiGlobals::isEven($chasy)) {
                          $nazvanie = ApiGlobals::to_trimmed_text($nazvanie);
                          $soderzhanie = ApiGlobals::to_trimmed_text($soderzhanie);
                          $sql = 'UPDATE tema
                               set nazvanie = :nazvanie,
                                   soderzhanie = :soderzhanie,
                                   tip_raboty = :tip_raboty,
                                   prepodavatel_fiz_lico = :prepodavatel,
                                   chasy = :chasy,
                                   nedelya = :nedelya,
                                   prepodavatel_vakansiya = :vakansiya
                               where id = :id
                              ';
                          if (Yii::$app->db->createCommand($sql)
                              ->bindValue(':nazvanie', $nazvanie)
                              ->bindValue(':soderzhanie', $soderzhanie)
                              ->bindValue(':tip_raboty', $vid_rabot)
                              ->bindValue(':prepodavatel', $prepodavatel)
                              ->bindValue(':chasy', $chasy)
                              ->bindValue(':id', $theme_id)
                              ->bindValue(':nedelya',$week)
                              ->bindValue(':vakansiya',$is_vakansiya,PDO::PARAM_BOOL)
                              ->execute()
                          ) {
                              $answer['res'] = 'done';
                              $item = KursGlobals::get_theme_by_id($theme_id);
                              $answer['html'] = KursGlobals::get_theme_row($item, StatusProgrammyKursa::REDAKTIRUETSYA, false);
                          } else {
                              $answer['res'] = 'error';
                              $answer['msg'] = 'Ошибка! Тема не обновлена. Ошибка в запросе к базе данных';
                          }
                      }
                      else{
                          $answer['res'] = 'error';
                          $answer['msg'] = 'Количество часов должно быть кратно 2';
                      }
                  }
                  else{
                      $answer['res'] = 'error';
                      $answer['msg'] = 'Введите название';
                  }
            break;
            case 'delete_theme'://удалить тему
                $theme_id = $_POST['theme_id'];
                $is_have_umk_or_cc = KursGlobals::is_theme_have_umk_or_cc($theme_id);
                if (!$is_have_umk_or_cc) {
                    $sql = 'DELETE FROM tema where id = :id';
                    if (Yii::$app->db->createCommand($sql)->bindValue(':id', $theme_id)->execute()) {
                        $answer['res'] = 'done';
                    } else {
                        $answer['res'] = 'error';
                        $answer['type'] = 'danger';
                        $answer['msg'] = 'Ошибка выполнения запроса, тема не удалена!';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['type'] = 'warning';
                    $answer['msg'] = 'Тема не удалена! Сначала удалите все УМК и форму контроля';
                }
            break;
            case 'add_umk':// добавить УМК
                $theme_id = $_POST['theme_id'];
                $umk_type = $_POST['umk_type'];
                $file = $_POST['file'];
                $url = $_POST['url'];
                $opisanie = $_POST['opisanie'];
                $tip_kursa = $_POST['tip_kursa'];
                $tip = $_POST['tip'];
                if (($umk_type==1 and !$file) or ($umk_type==2 and !$url)){
                    $answer['res']='nothing';
                }
                else{
                    $umk_id = KursGlobals::insert_umk($_POST);
                    if ($umk_id){
                        $sql = 'INSERT INTO umk_temy (tema, umk) VALUES (:tema,:umk)';
                        $res = Yii::$app->db->createCommand($sql)->bindValue(':tema',$theme_id)->bindValue(':umk',$umk_id)->execute();
                        if ($res){
                            $answer['res']='done';
                            $umk_item = KursGlobals::get_umk_by_id($umk_id);
                            $umk_item['theme_id'] = $theme_id;
                            $umk_item['tip_kursa'] = $tip_kursa;
                            $umk_item['tip'] = $tip;
                            $answer['html'] = KursGlobals::get_umk_row($umk_item);
                        }
                        else{
                            $answer['res']='error';
                            $answer['type']='danger';
                            $answer['msg'] = 'УМК не добален! Ошибка запроса к базе данных!';
                        }
                    }
                    else{
                        $answer['res']='error';
                        $answer['type']='danger';
                        $answer['msg'] = 'УМК не добален! Ошибка запроса к базе данных!';
                    }

                }
            break;
            case 'edit_umk'://Редактировать УМК
                $umk_id = $_POST['umk_id'];
                $umk_type = $_POST['umk_type'];
                $file = $_POST['file'];
                $url = $_POST['url'];
                $tip_kursa = $_POST['tip_kursa'];
                $tip = $_POST['tip'];
                if (($umk_type==1 and !$file) or ($umk_type==2 and !$url)){
                    $answer['res']='nothing';
                }
                else{
                    $res = KursGlobals::update_umk($_POST);
                    if ($res){
                        $answer['res']='done';
                        $umk_item = KursGlobals::get_umk_by_id($umk_id);
                        $umk_item['tip_kursa'] = $tip_kursa;
                        $umk_item['tip'] = $tip;
                        $answer['html'] = KursGlobals::get_umk_row($umk_item,StatusProgrammyKursa::REDAKTIRUETSYA,false);
                    }
                    else{
                        $answer['res']='error';
                        $answer['type']='danger';
                        $answer['msg'] = 'УМК не изменен! Ошибка запроса к базе данных!';
                    }

                }
            break;
            case 'delete_umk'://Удалить УМК
                $umk_id = $_POST['umk_id'];
                $is_error = false;
                $tip = $_POST['tip'];
                if ($tip==1)
                    $sql = 'DELETE FROM umk_podrazdela_kursa WHERE umk = :umk ';
                else
                    $sql = 'DELETE FROM umk_temy WHERE umk = :umk ';
                $res = Yii::$app->db->createCommand($sql)->bindValue(':umk',$umk_id)->execute();
                if ($res){
                    if (!KursGlobals::delete_umk($umk_id)) $is_error = true;
                }
                else $is_error = true;
                if (!$is_error){
                    $answer['res'] = 'done';
                }
                else{
                    $answer['res']='error';
                    $answer['type']='danger';
                    $answer['msg'] = 'УМК не удален! Ошибка запроса к базе данных!';
                }
            break;
            case 'save_kf'://Добавить форму котроля
                $theme_id = $_POST['theme_id'];
                $forma_kf_id = $_POST['forma_kf_id'];
                $tip_kursa = $_POST['tip_kursa'];
                $sql = 'UPDATE tema set forma_kontrolya = :forma_kontrolya where id=:id';
                if (Yii::$app->db->createCommand($sql)
                                 ->bindValue(':forma_kontrolya',$forma_kf_id)
                                 ->bindValue(':id',$theme_id)
                    ->execute()
                ){
                    $answer['res'] = 'done';
                    $kf =  KursGlobals::get_kf_by_theme_id($theme_id);
                    $kf['tip_kursa'] = $tip_kursa;
                    $answer['html'] = KursGlobals::get_kf_row($kf);
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Контрольная форма не добавлена!';
                }
            break;
            case 'save_edit_kf'://редактировать форму котроля
                $theme_id = $_POST['theme_id'];
                $kf_id = $_POST['kf_id'];
                $tip_kursa = $_POST['tip_kursa'];
                $sql = 'UPDATE tema set forma_kontrolya = :kf where id = :id';
                if (Yii::$app->db->createCommand($sql)
                                 ->bindValue(':kf',$kf_id)
                                 ->bindValue(':id',$theme_id)
                    ->execute()
                ){
                    $answer['res'] = 'done';
                    $kf = KursGlobals::get_kf_by_theme_id($theme_id);
                    $kf['tip_kursa'] = $tip_kursa;
                    $answer['html'] = KursGlobals::get_kf_row($kf,StatusProgrammyKursa::REDAKTIRUETSYA,false);
                }
                else $answer['res'] = 'error';
            break;
            case 'delete_kf'://удалить контрольную форму
                $theme_id = $_POST['theme_id'];
                $is_have_kim = KursGlobals::is_kf_have_kim($theme_id);
                if (!$is_have_kim) {
                    $sql = 'UPDATE tema set forma_kontrolya = null where id = :id';
                    if (Yii::$app->db->createCommand($sql)->bindValue(':id', $theme_id)->execute()) {
                        $answer['res'] = 'done';
                    } else {
                        $answer['res'] = 'error';
                        $answer['type'] = 'danger';
                        $answer['msg'] = 'Ошибка выполнения запроса, тема не удалена!';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['type'] = 'warning';
                    $answer['msg'] = 'Форма контроля не удалена! Сначала удалите КИМы.';
                }
            break;
            case 'save_kim'://сохранить КИМ
                $theme_id = $_POST['theme_id'];
                $tip_kursa = $_POST['tip_kursa'];
                $tip = $_POST['tip'];
                $is_error = false;
                $kim_id = KursGlobals::insert_kim($_POST);
                if ($kim_id){
                    $sql = 'INSERT INTO kim_temy (tema,kim) VALUES(:tema,:kim)';
                    $res = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':tema',$theme_id)
                                        ->bindValue(':kim',$kim_id)
                            ->execute();
                    if (!$res) $is_error = true;
                }
                else $is_error=true;
                if (!$is_error){
                    $answer['res'] = 'done';
                    $kim = KursGlobals::get_kim_by_id($kim_id);
                    $kim['tip_kursa'] = $tip_kursa;
                    $kim['tip'] = $tip;
                    $answer['html'] = KursGlobals::get_kim_row($kim);
                }
                else $answer['res'] = 'error';

            break;
            case 'delete_kim'://удалить КИМ
                $kim_id = $_POST['kim_id'];
                $tip = $_POST['tip'];
                switch ($tip){
                    case 1:
                        $sql = 'DELETE FROM kim_podrazdela_kursa WHERE kim = :kim';
                    break;
                    case 2:
                        $sql = 'DELETE FROM kim_temy WHERE kim = :kim';
                    break;
                    case 3:
                        $sql = 'DELETE FROM kim_kursa WHERE kim = :kim';
                    break;
                }

                $res = Yii::$app->db->createCommand($sql)->bindValue(':kim',$kim_id)->execute();
                $is_error =false;
                if ($res){
                    if (!KursGlobals::delete_kim($kim_id)) $is_error = true;
                }
                else $is_error = true;
                if (!$is_error){
                    $answer['res'] = 'done';
                }
                else $answer['res'] = 'error';
            break;
            case 'save_edit_kim': //Редактировать КИМ
                $kim_id = $_POST['kim_id'];
                $tip_kursa = $_POST['tip_kursa'];
                $tip = $_POST['tip'];
                if (KursGlobals::update_kim($_POST)){
                    $answer['res'] = 'done';
                    $kim = KursGlobals::get_kim_by_id($kim_id);
                    $kim['tip_kursa'] = $tip_kursa;
                    $kim['tip'] = $tip;
                    $answer['html'] = KursGlobals::get_kim_row($kim);
                }
                else {
                    $answer['res'] = 'error';
                }
            break;
            case 'save_fiak'://сохранить Итоговую аттестацию
                $kurs_id =  $_POST['kurs_id'];
                $fiak_id = $_POST['fiak_id'];
                $chasy = $_POST['chasy'];
                $opisanie = ApiGlobals::to_trimmed_text($_POST['opisanie']);
                $prepods = isset($_POST['prepods']) ? $_POST['prepods'] : [];
                $week = $_POST['week'];
                //file_put_contents('1.txt',print_r($_POST['prepods'],true));
                if (!$opisanie) $opisanie = null;
                if (!preg_match('/^\+?\d+$/', $chasy)) {
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Поле часы должно быть положительным целым числом';
                }
                else{
                    $sql = 'UPDATE kurs SET
                                  forma_itogovoj_attestacii = :fiak_id,
                                  chasy_itogovoj_attestacii = :chasy,
                                  opisanie_itogovoj_attestacii = :opisanie,
                                  nedelya_itogovoj_attestacii = :nedelya
                            WHERE id = :id';
                    $res = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':fiak_id',$fiak_id)
                                        ->bindValue(':chasy',$chasy)
                                        ->bindValue(':opisanie',$opisanie)
                                        ->bindValue(':nedelya',$week)
                                        ->bindValue(':id',$kurs_id)
                           ->execute();
                    if ($res) {
                        $is_error = false;
                        if ($prepods){
                            $t = Yii::$app->db->beginTransaction();
                            foreach ($prepods as $k=>$v) {
                                if ($v == -1){
                                    $v = null;
                                    $is_vakansiya = true;
                                }
                                else $is_vakansiya = null;
                                $sql = 'INSERT INTO kontroliruyuschij_kursa (kontroliruyuschij_fiz_lico, kurs, kontroliruyuschij_vakansiya)
                                        VALUES (:kontroliruyuschij, :kurs, :vakansiya)';
                                $res = Yii::$app->db->createCommand($sql)
                                                    ->bindValue(':kontroliruyuschij',$v)
                                                    ->bindValue(':kurs',$kurs_id)
                                                    ->bindValue(':vakansiya',$is_vakansiya)
                                    ->execute();
                                //file_put_contents('1.txt',print_r($res,true));
                                if (!$res){
                                    $is_error=true;
                                    break;
                                }
                            }
                            if ($is_error){
                                $t->rollBack();
                            }
                            else{
                                $t->commit();
                            }
                        }
                        if (!$is_error) {
                            $answer['res'] = 'done';
                            $fiak = KursGlobals::get_itogovaya_attestaciya_by_kurs_id($kurs_id);
                            $answer['html'] = KursGlobals::get_fiak_row($fiak);
                        }
                        else{
                            $answer['res'] = 'error';
                            $answer['msg'] = 'Итоговая аттестация добавлена! Возникла ошибка при добавлении списка преподавателей!';
                        }
                    }
                    else{
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Итоговая аттестация не добавлена! Ошибка выполнения запроса к базе данных!';
                    }
                }
            break;
            case 'delete_fiak':
                $kurs_id = $_POST['kurs_id'];
                $sql = 'UPDATE kurs SET forma_itogovoj_attestacii = null, chasy_itogovoj_attestacii=null, nedelya_itogovoj_attestacii = null
                        where id = :id';
                if (!KursGlobals::is_itgovaiya_attestatciya_have_themes_dr($kurs_id)) {
                    $res = Yii::$app->db->createCommand($sql)->bindValue(':id', $kurs_id)->execute();
                    if ($res) {
                        $sql = 'DELETE FROM kontroliruyuschij_kursa where kurs = :kurs';
                        $res = Yii::$app->db->createCommand($sql)->bindValue(':kurs',$kurs_id)->execute();
                        $answer['res'] = 'done';
                    } else {
                        $answer['res'] = 'error';
                        $answer['type'] = 'danger';
                        $answer['msg'] = 'Ошибка выполнения запроса к базе данных!';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['type'] = 'warning';
                    $answer['msg'] = 'Сначала удалите все темы дипломных работ';
                }
            break;
            case 'edit_fiak':
                $kurs_id = $_POST['kurs_id'];
                $fiak_id = $_POST['fiak_id'];
                $chasy = $_POST['chasy'];
                $opisanie = ApiGlobals::to_trimmed_text($_POST['opisanie']);
                $prepods = isset($_POST['prepods']) ? $_POST['prepods'] : [];
                $week = $_POST['week'];
                if (!$opisanie) $opisanie = null;
                if (!ApiGlobals::is_posistive($chasy)) {
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Поле часы должно быть положительным целым числом';
                }
                else{
                    $sql = 'UPDATE kurs SET
                                forma_itogovoj_attestacii = :fiak_id,
                                chasy_itogovoj_attestacii=:chasy,
                                opisanie_itogovoj_attestacii=:opisanie,
                                nedelya_itogovoj_attestacii=:nedelya
                             where id=:id';
                    $res = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':fiak_id',$fiak_id)
                                        ->bindValue(':chasy',$chasy)
                                        ->bindValue(':opisanie',$opisanie)
                                        ->bindValue(':nedelya',$week)
                                        ->bindValue(':id',$kurs_id)
                           ->execute();
                    $is_error = false;
                    if ($res){
                        $t = Yii::$app->db->beginTransaction();
                        $sql = 'DELETE FROM kontroliruyuschij_kursa where kurs=:kurs';
                        if (Yii::$app->db->createCommand($sql)->bindValue(':kurs',$kurs_id)->execute()!==false) {
                            if ($prepods) {
                                foreach ($prepods as $k => $v) {
                                    if ($v == -1){
                                        $v = null;
                                        $is_vakansiya = true;
                                    }
                                    else $is_vakansiya = null;
                                    $sql = 'INSERT INTO kontroliruyuschij_kursa (kontroliruyuschij_fiz_lico, kurs,kontroliruyuschij_vakansiya)
                                    VALUES (:kontroliruyuschij, :kurs, :vakanciya)';
                                    $res = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':kontroliruyuschij', $v)
                                        ->bindValue(':kurs', $kurs_id)
                                        ->bindValue(':vakanciya',$is_vakansiya)
                                        ->execute();
                                    if (!$res) {
                                        $is_error = true;
                                        break;
                                    }
                                }
                            }
                        }
                        else {
                            $is_error = true;
                        }
                        if ($is_error){
                            $t->rollBack();
                        }
                        else{
                            $t->commit();
                        }
                    }
                    if (!$is_error) {

                        $answer['res'] = 'done';
                        $fiak = KursGlobals::get_itogovaya_attestaciya_by_kurs_id($kurs_id);
                        $answer['html'] = KursGlobals::get_fiak_row($fiak,StatusProgrammyKursa::REDAKTIRUETSYA,false);
                    }
                    else{
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Итоговая аттестация не обновлена! Ошибка запроса к базе данных!';
                    }
                }
            break;
            case 'save_theme_dr':
                $theme_name = ApiGlobals::to_trimmed_text($_POST['theme_name']);
                $kurs_id = $_POST['kurs_id'];
                $sql = 'INSERT INTO tema_diplomnoj_raboty_kursa (kurs, nazvanie) VALUES (:kurs,:nazvanie)';
                $res = Yii::$app->db->createCommand($sql)
                                    ->bindValue(':kurs',$kurs_id)
                                    ->bindValue(':nazvanie',$theme_name)
                        ->execute();
                if ($res){
                    $answer['res'] = 'done';
                    $theme_dr_id = Yii::$app->db->getLastInsertID('tema_diplomnoj_raboty_kursa_id_seq');
                    $theme_dr = KursGlobals::get_theme_dr_by_id($theme_dr_id);
                    $answer['html'] = KursGlobals::get_theme_dr_row($theme_dr);
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Тема не добавлена! Ошибка выполнения запроса к базе даных';
                }
            break;
            case 'edit_theme_dr':
                $kurs_id = $_POST['kurs_id'];
                $theme_name = $_POST['theme_name'];
                $theme_dr_id = $_POST['theme_dr_id'];
                $sql = 'UPDATE tema_diplomnoj_raboty_kursa SET nazvanie=:name where id = :id';
                $res = Yii::$app->db->createCommand($sql)
                                    ->bindValue(':name',$theme_name)
                                    ->bindValue(':id',$theme_dr_id)
                        ->execute();
                if ($res){
                    $answer['res'] = 'done';
                    $theme_dr = KursGlobals::get_theme_dr_by_id($theme_dr_id);
                    $answer['html'] = KursGlobals::get_theme_dr_row($theme_dr);
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Изменения не сохранены! Произошла ошибка выполнения запроса к базе данных.';
                }
            break;
            case 'delete_theme_dr':
                $theme_dr_id = $_POST['theme_dr_id'];
                $sql = 'DELETE FROM tema_diplomnoj_raboty_kursa where id =:id';
                if (Yii::$app->db->createCommand($sql)->bindValue(':id',$theme_dr_id)->execute()){
                    $answer['res'] = 'done';
                }
                else{
                    $answer['res'] = 'error';
                }
            break;
            case 'add_podrazdel_kf':
                $podrazdel_id = $_POST['podrazdel_id'];
                $chasy = $_POST['chasy'];
                $kf_id =  $_POST['kf_id'];
                if ($kf_id == -1) $kf_id = null;
                $prepods = isset($_POST['prepods']) ? $_POST['prepods'] : [];
                $sql = 'UPDATE podrazdel_kursa SET forma_kontrolya =:kf, chasy_kontrolya =:chasy WHERE id = :id';
                if (ApiGlobals::is_posistive($chasy) and ApiGlobals::isEven($chasy)) {
                    $res = Yii::$app->db->createCommand($sql)
                                        ->bindValue(':kf',$kf_id)
                                        ->bindValue(':chasy',$chasy)
                                        ->bindValue(':id',$podrazdel_id)
                            ->execute();

                    if ($res) {
                        $is_error = false;
                        if ($prepods){
                            $t = Yii::$app->db->beginTransaction();
                            foreach ($prepods as $k=>$v) {
                                //file_put_contents('1.txt',$v);
                                if($v == -1) {
                                    $is_vakansiya = true;
                                    $v = null;
                                }
                                else $is_vakansiya = null;
                                $sql = 'INSERT INTO kontroliruyuschij_podrazdela_kursa (kontroliruyuschij_fiz_lico, podrazdel_kursa, kontroliruyuschij_vakansiya)
                                        VALUES (:kontroliruyuschij, :podrazdel,:vakanciya)';
                                $res = Yii::$app->db->createCommand($sql)
                                    ->bindValue(':kontroliruyuschij',$v)
                                    ->bindValue(':podrazdel',$podrazdel_id)
                                    ->bindValue(':vakanciya',$is_vakansiya)
                                    ->execute();
                                if (!$res){
                                    $is_error=true;
                                    break;
                                }
                            }
                            if ($is_error){
                                $t->rollBack();
                            }
                            else{
                                $t->commit();
                            }
                        }
                        if (!$is_error) {
                            $answer['res'] = 'done';
                            $kf_podrazdela = KursGlobals::get_kf_podrazdela_by_id($podrazdel_id);
                            $answer['html'] = KursGlobals::get_kf_podrazdela_row($kf_podrazdela);
                        }
                        else{
                            $answer['res'] = 'error';
                            $answer['msg'] = 'Произошла ошибка выполнения запроса к базе данных.';
                        }
                    } else {
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Произошла ошибка выполнения запроса к базе данных. '.$res;
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Количество часов должно быть положительным целым числом кратное 2';
                }
            break;
            case 'edit_podrazdel_kf':
                $podrazdel_id = $_POST['podrazdel_id'];
                $chasy = $_POST['chasy'];
                $kf_id =  $_POST['kf_id'];
                if ($kf_id == -1) $kf_id = null;
                $prepods = isset($_POST['prepods']) ? $_POST['prepods']: [];
                $sql = 'UPDATE podrazdel_kursa SET forma_kontrolya =:kf, chasy_kontrolya =:chasy WHERE id = :id';
                if (ApiGlobals::is_posistive($chasy) and ApiGlobals::isEven($chasy)) {
                    $res = Yii::$app->db->createCommand($sql)
                            ->bindValue(':kf',$kf_id)
                            ->bindValue(':chasy',$chasy)
                            ->bindValue(':id',$podrazdel_id)
                        ->execute();
                    if ($res) {
                        $is_error=false;
                        $t = Yii::$app->db->beginTransaction();
                        $sql = 'DELETE FROM kontroliruyuschij_podrazdela_kursa where podrazdel_kursa = :podrazdel';
                        Yii::$app->db->createCommand($sql)->bindValue(':podrazdel',$podrazdel_id)->execute();
                            foreach ($prepods as $k => $v) {
                                if($v == -1) {
                                    $is_vakansiya = true;
                                    $v = null;
                                }
                                else $is_vakansiya = null;
                                $sql = 'INSERT INTO kontroliruyuschij_podrazdela_kursa (kontroliruyuschij_fiz_lico, podrazdel_kursa, kontroliruyuschij_vakansiya)
                                        VALUES (:kontroliruyuschij, :podrazdel,:vakanciya)';
                                $res = Yii::$app->db->createCommand($sql)
                                    ->bindValue(':kontroliruyuschij', $v)
                                    ->bindValue(':podrazdel', $podrazdel_id)
                                    ->bindValue(':vakanciya',$is_vakansiya)
                                    ->execute();
                                if (!$res) {
                                    $is_error = true;
                                    break;
                                }
                            }
                        if ($is_error){
                            $t->rollBack();
                            $answer['res'] = 'error';
                            $answer['msg'] = 'Произошла ошибка при добавлении преподавателей';
                        }
                        else{
                            $t->commit();
                            $answer['res'] = 'done';
                            $kf_podrazdela = KursGlobals::get_kf_podrazdela_by_id($podrazdel_id);
                            $answer['html'] = KursGlobals::get_kf_podrazdela_row($kf_podrazdela);
                        }

                    } else {
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Произошла ошибка выполнения запроса к базе данных';
                    }
                }
                else{
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Количество часов должно быть положительным целым числом кратное 2';
                }
            break;
            case 'delete_podrazdel_kf':
                $podrazdel_id = $_POST['podrazdel_id'];
                $sql = 'UPDATE podrazdel_kursa SET forma_kontrolya=null,chasy_kontrolya=null where id = :id';
                if (Yii::$app->db->createCommand($sql)->bindValue(':id',$podrazdel_id)->execute()){
                    $sql = 'DELETE FROM kontroliruyuschij_podrazdela_kursa where podrazdel_kursa=:podrazdel';
                    try{
                        Yii::$app->db->createCommand($sql)->bindValue(':podrazdel',$podrazdel_id)->execute();
                        $answer['res'] = 'done';
                    }
                    catch (Exception $e){
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Произошла ошибка выполнения запроса к базе данных (таблица kontroliruyuschij_podrazdela_kursa)'.$e->getMessage();
                    }
//                    if (Yii::$app->db->createCommand($sql)->bindValue(':podrazdel',$podrazdel_id)->execute())
//                        $answer['res'] = 'done';
//                    else{
//                        $answer['res'] = 'error';
//                        $answer['msg'] = 'Произошла ошибка выполнения запроса к базе данных (таблица kontroliruyuschij_po_podrazdelu_kursa)';
//                    }
                }
                else {
                    $answer['res'] = 'error';
                    $answer['msg'] = 'Произошла ошибка выполнения запроса к базе данных (таблица podrazdel_kursa)';
                }
            break;
            case 'save_podrazdel_kim':
                $podrazdel_id = $_POST['podrazdel_id'];
                $tip_kursa = $_POST['tip_kursa'];
                $tip = $_POST['tip'];
                $is_error = false;
                $kim_id = KursGlobals::insert_kim($_POST);
                if ($kim_id){
                    $sql = 'INSERT INTO kim_podrazdela_kursa (podrazdel_kursa,kim) VALUES(:pk,:kim)';
                    $res = Yii::$app->db->createCommand($sql)
                        ->bindValue(':pk',$podrazdel_id)
                        ->bindValue(':kim',$kim_id)
                        ->execute();
                    if (!$res) $is_error = true;
                }
                else $is_error=true;
                if (!$is_error){
                    $answer['res'] = 'done';
                    $kim = KursGlobals::get_kim_by_id($kim_id);
                    $kim['tip_kursa'] = $tip_kursa;
                    $kim['tip'] = $tip;
                    $answer['html'] = KursGlobals::get_kim_row($kim);
                }
                else $answer['res'] = 'error';
            break;
            case 'add_podrazdel_umk':
                $podrazdel_id = $_POST['podrazdel_id'];
                $umk_type = $_POST['umk_type'];
                $file = $_POST['file'];
                $url = $_POST['url'];
                $opisanie = $_POST['opisanie'];
                $tip_kursa = $_POST['tip_kursa'];
                $tip = $_POST['tip'];
                if (($umk_type==1 and !$file) or ($umk_type==2 and !$url)){
                    $answer['res']='nothing';
                }
                else{
                    $umk_id = KursGlobals::insert_umk($_POST);
                    if ($umk_id){
                        $sql = 'INSERT INTO umk_podrazdela_kursa (podrazdel_kursa, umk) VALUES (:pk,:umk)';
                        $res = Yii::$app->db->createCommand($sql)->bindValue(':pk',$podrazdel_id)->bindValue(':umk',$umk_id)->execute();
                        if ($res){
                            $answer['res']='done';
                            $umk_item = KursGlobals::get_umk_by_id($umk_id);
                            $umk_item['tip_kursa'] = $tip_kursa;
                            $umk_item['tip'] = $tip;
                            //$umk_item['podrazdel'] = $podrazdel_id;
                            $answer['html'] = KursGlobals::get_umk_row($umk_item);
                        }
                        else{
                            $answer['res']='error';
                            $answer['type']='danger';
                            $answer['msg'] = 'УМК не добален! Ошибка запроса к базе данных!';
                        }
                    }
                    else{
                        $answer['res']='error';
                        $answer['type']='danger';
                        $answer['msg'] = 'УМК не добален! Ошибка запроса к базе данных!';
                    }

                }
            break;
            case 'save_podrazdel_num_order':
                $order = $_POST['order'];
                if ($order){
                    $t = Yii::$app->db->beginTransaction();
                    $is_error = false;
                    foreach ($order as $k => $v) {
                        $sql = 'UPDATE podrazdel_kursa SET nomer = :nomer WHERE id = :id';
                        $res = Yii::$app->db->createCommand($sql)
                            ->bindValue(':nomer', $v['new'])
                            ->bindValue(':id', $k)
                            ->execute();
                        if (!$res){
                            $is_error = true;
                            break;
                        }
                    }
                    if (!$is_error){
                        $t->commit();
                        $answer['res'] = 'done';
                    }
                    else{
                        $t->rollBack();
                        $answer['res'] = 'error';
                        $answer['type'] = 'error';
                        $answer['msg'] = 'Во время сохрарения произошла ошибка. Данные не изменены.ƒ';
                    }
                }
            break;
            case 'save_theme_num_order':
                $order = $_POST['order'];
                if ($order) {
                    $t = Yii::$app->db->beginTransaction();
                    $is_error = false;
                    foreach ($order as $k => $v) {
                        $sql = 'UPDATE tema SET nomer = :nomer WHERE id = :id';
                        $res = Yii::$app->db->createCommand($sql)
                            ->bindValue(':nomer', $v['new'])
                            ->bindValue(':id', $k)
                            ->execute();
                        if (!$res){
                            $is_error = true;
                            break;
                        }
                    }
                    if (!$is_error){
                        $t->commit();
                        $answer['res'] = 'done';
                    }
                    else{
                        $t->rollBack();
                        $answer['res'] = 'error';
                        $answer['type'] = 'error';
                        $answer['msg'] = 'Во время сохрарения произошла ошибка. Данные не изменены.ƒ';
                    }
                }

            break;
            case 'check_kurs':
                $kurs_id = $_POST['kurs_id'];
                $is_checked = $_POST['is_checked'];
                $is_error = false;
                $answer['is_set_podpis'] = 1;
                //file_put_contents('1.txt',print_r($_POST,true));
                if ($is_checked){
                    if (KursGlobals::is_var_razdel_has_error($kurs_id)){
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Количество часов в блоках тем/дисциплинах вариативной части должно быть равным количеству часов первого блока тем/дисциплины вариативной части';
                    }
                    else{
                        //$sql = 'UPDATE kurs SET status_programmy = \'zavershena\' where id = :id';
                        $res = KursGlobals::set_kurs_status($kurs_id,'zavershena');
                        if ($res){
                            $answer['res'] = 'done';
                        }
                        else{
                            $answer['res'] = 'error';
                            $answer['msg'] = 'Ошибка выполнения запроса к базе данных. Подпись не сохранена';
                        }
                    }
                }
                else{
                    //$sql = 'UPDATE kurs SET status_programmy = \'redaktiruetsya\' where id = :id';
                    $res = KursGlobals::set_kurs_status($kurs_id,'redaktiruetsya');
                    if ($res){
                        $answer['res'] = 'done';
                    }
                    else{
                        $answer['res'] = 'error';
                        $answer['msg'] = 'Ошибка выполнения запроса к базе данных. Подпись не сохранена';
                    }
                }

            break;
            case 'save_kurs_kim':
                $kurs_id = $_POST['kurs_id'];
                $tip_kursa = $_POST['tip_kursa'];
                $is_error = false;
                $kim_id = KursGlobals::insert_kim($_POST);
                $tip = $_POST['tip'];
                if ($kim_id){
                    $sql = 'INSERT INTO kim_kursa (kurs,kim) VALUES(:kurs,:kim)';
                    $res = Yii::$app->db->createCommand($sql)
                        ->bindValue(':kurs',$kurs_id)
                        ->bindValue(':kim',$kim_id)
                        ->execute();
                    if (!$res) $is_error = true;
                }
                else $is_error=true;
                if (!$is_error){
                    $answer['res'] = 'done';
                    $kim = KursGlobals::get_kim_by_id($kim_id);
                    $kim['tip_kursa'] = $tip_kursa;
                    $kim['tip'] = $tip;
                    $answer['html'] = KursGlobals::get_kim_row($kim);
                }
                else $answer['res'] = 'error';
            break;
        }
        return json_encode($answer);
    }

    private function canEdit($fiz_lico_id, $kurs_id)
    {
        $query = new Query();

        $condition = [
            'rukovoditel' => $fiz_lico_id,
            'id' => $kurs_id
        ];

        $count = $query->from('kurs')->where($condition)->count();

        return $count===1;
    }
}