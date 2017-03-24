<?php
/**
 * Created by PhpStorm.
 * User: macbook22
 * Date: 25.04.15
 * Time: 10:09
 */

namespace app\controllers;

use app\globals\ApiGlobals;
use app\globals\KursGlobals;
use app\globals\RpdGlobals;
use app\models\podrazdel_kursa\PodrazdelKursa;
use kartik\mpdf\Pdf;

use yii\web\Controller;

class PdfController extends Controller{

    public function actionKurs(){
        if (!$id = $_GET['id']) $id = 23;

        $kurs = KursGlobals::get_kurs($id);
        $god = substr($kurs['plan_prospekt_god'],0,4);
        $kug = KursGlobals::get_kug($id);
        $attestaciya = KursGlobals::get_attestatciya($id);
        $max_week_num = KursGlobals::get_max_week_of_kurs($id);
        $plan_html = KursGlobals::get_uchebnii_plan_html($kug,$attestaciya);
        $kug_html = KursGlobals::get_kug_html($kug,$attestaciya,$max_week_num);
        $soderzhanie = KursGlobals::get_soderzhanie($id);
        $rukovoditel = KursGlobals::get_rukovoditel_podrazdeleniya($kurs['strukturnoe_podrazdelenie']);
        $kims = KursGlobals::get_kims($id);
        $pdf = (new Pdf)->api;

        $pdf->title = $kurs['nazvanie'];

        $pdf->WriteHTML('<style>
                body {font-family: "Times New Roman", Times, serif;font-size: 10pt;}
                p.myp {text-indent: 2.5em;text-align: justify}
                .center {text-align:center}
                .tb {border-collapse: collapse}
                .tb td {padding: 5px;border: 1px solid #000}
                .razdel-tr td {font-weight: bold}
                .attestatsiya-tr td {font-weight: bold}
                .bold-tr td {font-weight: bold}
                </style>');
        $pdf->WriteHTML('<p style="text-align:center">Министерство образования и науки Республики Бурятия</p>');
        $pdf->WriteHTML('<p style="text-align:center">ГАУ ДПО РБ «Бурятский республиканский институт образовательной политики»</p>');
        $pdf->WriteHTML('<p style="text-align:right">УТВЕРЖДАЮ</p>');
        if ($kurs['tip'] == 'pk') {
            $pdf->WriteHTML('<p style="text-align:right">Проректор по организации</p>');
            $pdf->WriteHTML('<p style="text-align:right">образовательной деятельности</p>');
            $pdf->WriteHTML('<p style="text-align:right">_____________ / ______________</p>');
        }
        else{
            $pdf->WriteHTML('<p style="text-align:right">Ректор ГАУ ДПО РБ «БРИОП»</p>');
            $pdf->WriteHTML('<p style="text-align:right">_____________ / Г.Н. Фомицкая</p>');
        }
        $pdf->WriteHTML('<p style="text-align:right">«____»______________20____ г.</p>');
        $pdf->WriteHTML('<div style="position: absolute;top: 40%;left:0;width:100%">
                <p style="text-align:center;">'.($kurs['tip'] == 'po' ? 'Основная профессиональная программа' : 'Дополнительная профессиональная программа').'</p>');
        if ($kurs['tip'] == 'pk')
            $pdf->WriteHTML ('<p style="text-align:center;">повышения квалификации</p>');
        if ($kurs['tip'] == 'po')
            $pdf->WriteHTML ('<p style="text-align:center;">профессионального обучения</p>');
        if ($kurs['tip'] == 'pp')
            $pdf->WriteHTML ('<p style="text-align:center;">профессиональной переподготовки</p>');
        $pdf->WriteHTML($this->get_empty_row(2).'
            <div style="text-align:center;width: 100%;">
                <p style="font-weight:bold;width: 60%;margin: 0 auto">«'.$kurs['nazvanie'].'»</p>
            </div>
            </div>
        ');
        $pdf->WriteHTML('<htmlpagefooter show-this-page="1" name="first_page_footer">
                         <div style="text-align: center"><p>Улан-Удэ</p><p>'.$god.' год</p></div>
                        </htmlpagefooter>');
        $pdf->SetHTMLFooterByName('first_page_footer');
        $pdf->AddPage();
        $pdf->WriteHTML('<p>Структурное подразделение: '.ApiGlobals::first_letter_up($kurs['podrazdelenie']).'</p>');
        if ($kurs['tip'] == 'pk')
            $pdf->WriteHTML('<p>Составитель: '.
                                                $kurs['rukovoditel_familiya'].
                                                ' '.
                                                ApiGlobals::get_first_letter($kurs['rukovoditel_imya']).
                                                '. '.
                                                ApiGlobals::get_first_letter($kurs['rukovoditel_otchestvo']).
                                                '. '.
                                                ', '.$kurs['rukovoditel_dolzhnost'].'.</p>');
        else
            $pdf->WriteHTML('<p>Составители: '.$kurs['sostaviteli'].'.</p>');
        if ($kurs['tip'] != 'pk')
            $pdf->WriteHTML('<p>Рецензенты: '.$kurs['recenzenti'].'.</p>');
        $pdf->WriteHTML($this->get_empty_row(2));
        $pdf->WriteHTML('<p>Обсуждена на заседании '.
            ($kurs['podrazdelenie_sokrashennoe_nazvanie'] ?
             $kurs['podrazdelenie_sokrashennoe_nazvanie'] :
             $kurs['podrazdelenie']).'.</p>');
        $pdf->WriteHTML('<p>Протокол № ___ от «__» _________ 20__ г.</p>');
        if ($kurs['tip'] == 'pk') {
            $pdf->WriteHTML($this->get_empty_row(2));
            $pdf->WriteHTML('<p>Утверждена на заседании НМС.</p>');
            $pdf->WriteHTML('<p>Протокол № ___ от «__» _________ 20__ г.</p>');
        }
        else {
            $pdf->WriteHTML($this->get_empty_row(2));
            $pdf->WriteHTML('<p>Утверждена на заседании Ученого совета.</p>');
            $pdf->WriteHTML('<p>Протокол № ___ от «__» _________ 20__ г.</p>');
        }
        $pdf->WriteHTML('<htmlpagefooter show-this-page="1" name="numbered_page_footer">
                         <div style="text-align: center">{PAGENO}</div>
                        </htmlpagefooter>');
        $pdf->SetHTMLFooterByName('numbered_page_footer');
        $pdf->AddPage();
        $pdf->WriteHTML('<p style="text-align: center"><b>Пояснительная записка</b></p>');
        $pdf->WriteHTML($this->get_paragraph('Актуальность','style="font-weight:bold"'));
        $pdf->WriteHTML(ApiGlobals::parse_text($kurs['aktualnost']));
        $pdf->WriteHTML($this->get_paragraph('<b>Цель: </b>'.$kurs['cel']));
        $pdf->WriteHTML($this->get_paragraph('Задачи:','style="font-weight:bold"'));
        $pdf->WriteHTML(ApiGlobals::parse_text($kurs['zadachi']));
        if ($kurs['tip'] != 'pk'){
            if ($kurs['tip'] == 'pp'){
                $pdf->WriteHTML($this->get_paragraph('Характеристика нового вида деятельности:','style="font-weight:bold"'));
                $pdf->WriteHTML(ApiGlobals::parse_text($kurs['harakteristika_novogo_vida_deyatelnosti']));
            }
            else{
                $pdf->WriteHTML($this->get_paragraph('Характеристика новой квалификации:','style="font-weight:bold"'));
                $pdf->WriteHTML(ApiGlobals::parse_text($kurs['harakteristika_novoj_kvalifikacii']));
            }
        }
        $pdf->WriteHTML($this->get_paragraph('Планируемые результаты:','style="font-weight:bold"'));
        $pdf->WriteHTML(ApiGlobals::parse_text($kurs['planiruemye_rezultaty']));
        $pdf->WriteHTML($this->get_paragraph('Организационно-педагогические условия:','style="font-weight:bold"'));
        $pdf->WriteHTML($this->get_paragraph('<i>Информационные:</i> '.$kurs['informacionnye_usloviya']));
        $pdf->WriteHTML($this->get_paragraph('<i>Учебно-методические:</i> '.$kurs['uchebnometodicheskie_usloviya']));
        $pdf->WriteHTML($this->get_paragraph('<i>Кадровые:</i> '.$kurs['kadrovye_usloviya']));
        $pdf->WriteHTML($this->get_paragraph('<i>Материально-технические:</i> '.$kurs['tehnicheskie_usloviya'])); //($kurs['tip']=='pk' ? 'Материально-технические' : 'Технические')
        $pdf->WriteHTML($this->get_paragraph('<b>Категория слушателей:</b> '.$kurs['kategorii']));
        $pdf->WriteHTML($this->get_paragraph('<b>Количество часов: '.$kurs['raschitano_chasov'].'</b>'));
        if ($kurs['tip'] != 'pk'){
            $pdf->WriteHTML($this->get_paragraph('<b>Форма обучения: '.$kurs['forma_obucheniya'].'</b>'));
            $pdf->WriteHTML($this->get_paragraph('<b>Режим занятий: '.$kurs['rezhim_zanyatij'].'</b>'));
        }
        $pdf->WriteHTML($this->get_paragraph('<b>Итоговая аттестация: </b>'.(
            $kurs['itogovaya_attestaciya_tekst'] ?
                $kurs['itogovaya_attestaciya_tekst'] :
                $kurs['nazvanie_itogovoi_attestacii'])));
        $pdf->AddPage();
        $pdf->WriteHTML('<div class="center">Министерство образования и науки Республики Бурятия</div>');
        $pdf->WriteHTML('<div class="center">ГАУ ДПО РБ «Бурятский республиканский институт образовательной политики»</div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML('<div class="" style="text-align:right">УТВЕРЖДАЮ</div>');
        if ($kurs['tip'] == 'pk') {
            $pdf->WriteHTML('<div style="text-align:right">Проректор по организации</div>');
            $pdf->WriteHTML('<div style="text-align:right">образовательной деятельности</div>');
            $pdf->WriteHTML('<div style="text-align:right">_____________ / ______________</div>');
        }
        else{
            $pdf->WriteHTML('<div style="text-align:right">Ректор ГАУ ДПО РБ «БРИОП»</div>');
            $pdf->WriteHTML('<div style="text-align:right">________ / Г.Н. Фомицкая</div>');
        }
        $pdf->WriteHTML('<div class="" style="text-align:right">« ____» __________ 20__ г.</div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML('<div class="center"><b>УЧЕБНЫЙ ПЛАН</b></div>');
        if ($kurs['tip'] == 'pk')
            $pdf->WriteHTML('<div class="center">дополнительной профессиональной программы повышения квалификации</div>');
        if ($kurs['tip'] == 'po')
            $pdf->WriteHTML('<div class="center">основной профессиональной программы профессионального обучения</div>');
        if ($kurs['tip'] == 'pp')
            $pdf->WriteHTML('<div class="center">дополнительной профессиональной программы профессиональной переподготовки</div>');
        $pdf->WriteHTML('<div style="text-align:center;width: 100%;">
                            <p style="font-weight:bold;width: 60%;margin: 0 auto;text-align: center">«'.$kurs['nazvanie'].'»</p>
                        </div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML('<span style="text-align: left">Категория слушателей: '.$kurs['kategorii'].'</span>');
        $pdf->WriteHTML('<span style="text-align: left">Форма обучения: '.$kurs['forma_obucheniya_kursa'].'</span>');
        $pdf->WriteHTML('<span style="text-align: left">Режим занятий: '.$kurs['rezhim_zanyatij'].'</span>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML($plan_html);
        $pdf->WriteHTML('<p style="text-align:left;">Руководитель курсов: ____________/'.ApiGlobals::get_first_letter($kurs['rukovoditel_imya']).'.'.ApiGlobals::get_first_letter($kurs['rukovoditel_otchestvo']).'. '.$kurs['rukovoditel_familiya'].'</p>');
        $pdf->WriteHTML('<p style="text-align:left;">Руководитель структурного подразделения: __________/ '.ApiGlobals::get_first_letter($rukovoditel['imya']).'.'.ApiGlobals::get_first_letter($rukovoditel['otchestvo']).'. '.$rukovoditel['familiya'].'</p>');
        $pdf->WriteHTML('<p style="text-align:left;">Начальник учебного отдела: ___________/ Л.Е. Халудорова</p>');
        $pdf->AddPage();
        $pdf->WriteHTML('<div class="center">Министерство образования и науки Республики Бурятия</div>');
        $pdf->WriteHTML('<div class="center">ГАУ ДПО РБ «Бурятский республиканский институт образовательной политики»</div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML('<div class="" style="text-align:right">УТВЕРЖДАЮ</div>');
        if ($kurs['tip'] == 'pk') {
            $pdf->WriteHTML('<div style="text-align:right">Проректор по организации</div>');
            $pdf->WriteHTML('<div style="text-align:right">образовательной деятельности</div>');
            $pdf->WriteHTML('<div style="text-align:right">_____________ / ______________</div>');
        }
        else{
            $pdf->WriteHTML('<div style="text-align:right">Ректор ГАУ ДПО РБ «БРИОП»</div>');
            $pdf->WriteHTML('<div style="text-align:right">________ / Г.Н. Фомицкая</div>');
        }
        $pdf->WriteHTML('<div class="lh1" style="text-align:right">« ____» __________ 20__ г.</div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML('<div class="center"><b>КАЛЕНДАРНЫЙ УЧЕБНЫЙ ГРАФИК</b></div>');
        if ($kurs['tip'] == 'pk')
            $pdf->WriteHTML('<div class="center">дополнительной профессиональной программы повышения квалификации</div>');
        if ($kurs['tip'] == 'po')
            $pdf->WriteHTML('<div class="center">основной профессиональной программы профессионального обучения</div>');
        if ($kurs['tip'] == 'pp')
            $pdf->WriteHTML('<div class="center">дополнительной профессиональной программы профессиональной переподготовки</div>');
        $pdf->WriteHTML('<div class="lh1" style="text-align:center;width: 100%;">
                            <p style="font-weight:bold;width: 60%;margin: 0 auto;text-align: center">«'.$kurs['nazvanie'].'»</p>
                        </div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML($kug_html);
        $pdf->WriteHTML('<p style="text-align:left;">Руководитель курсов: ____________/'.ApiGlobals::get_first_letter($kurs['rukovoditel_imya']).'.'.ApiGlobals::get_first_letter($kurs['rukovoditel_otchestvo']).'. '.$kurs['rukovoditel_familiya'].'</p>');
        $pdf->WriteHTML('<p style="text-align:left;">Руководитель структурного подразделения: __________/ '.ApiGlobals::get_first_letter($rukovoditel['imya']).'.'.ApiGlobals::get_first_letter($rukovoditel['otchestvo']).'. '.$rukovoditel['familiya'].'</p>');
        $pdf->WriteHTML('<p style="text-align:left;">Начальник учебного отдела: ___________/ Л.Е. Халудорова</p>');
        if ($kurs['tip'] == 'pk') {
            $pdf->AddPage();
            $pdf->WriteHTML($this->get_paragraph('Содержание', 'style="font-weight:bold;text-align:center"'));
            $pdf->WriteHTML(KursGlobals::get_soderzhanie_html($soderzhanie));
            if ($attestaciya){
                $pdf->WriteHTML(
                    $this->get_paragraph(
                            '<b>Итоговая аттестация. '.ApiGlobals::first_letter_up($attestaciya['forma_attestacii'].' ('.$attestaciya['chasy'].' ч.)</b>'
                        )
                    )
                );
                if ($attestaciya['opisanie']){
                    $pdf->WriteHTML($this->get_paragraph($attestaciya['opisanie']));
                }
            }
        }
        //var_dump($kims);die();
        if ($kims) {
            $pdf->AddPage();
            $pdf->WriteHTML('<div class="center"><b>КОНТРОЛЬНО-ИЗМЕРИТЕЛЬНЫЕ МАТЕРИАЛЫ</b></div>');
            foreach ($kims as $item) {
                if ($kurs['tip'] == 'pk' or ($item['type']==2))
                    $pdf->WriteHTML(RpdGlobals::get_rpd_kim_list_item($item,$soderzhanie));
            }
        }
        if ($kurs['tip']=='pk') {
            $pdf->AddPage();
            $pdf->WriteHTML($this->get_paragraph('Литература', 'style="font-weight:bold;text-align:center"'));
            $pdf->WriteHTML(ApiGlobals::parse_text($kurs['spisok_literatury']));
        }
        $pdf->Output();
        die();
    }

    public function actionRpd(){
        if (!$id = $_GET['id']) $id = 2;
        $podrazdel = PodrazdelKursa::find()->where(['id'=>$id])->one();
        $kurs_info = RpdGlobals::get_kurs_info_by_podrazdel_id($id);
        $kug = RpdGlobals::get_kug($kurs_info['kurs_id']);
        $max_nedelya = 0;
        $min_nedelya = 0;
        $nedeli = RpdGlobals::get_max_min_weeks($id);
        $min_nedelya = min((int)$podrazdel['nedelya_nachalo'],$nedeli['min']);
        $max_nedelya = max($podrazdel['nedelya_konec'], $nedeli['max']);
        $kug_html = RpdGlobals::get_kug_html($kug,$id,$min_nedelya,$max_nedelya);
        $plan_html = RpdGlobals::get_uchebnii_plan_html($kug,$id);
        $rukovoditel = RpdGlobals::get_rukovoditel_podrazdela($id);
        $rukovoditel_podrazdeleniya = RpdGlobals::get_rukovoditel_podrazdeleniya($id);
        $tip = $kurs_info['kurs_tip'] == 'pp' ? 'профессиональной переподготовки' : 'профессионального обучения';
        $soderzhanie = RpdGlobals::get_rpd_soderzhanie($id);
        $nomer = RpdGlobals::get_nomer_razdela_v_kurse_by_podrazdel($kurs_info['kurs_id'],$id);
        $kims = RpdGlobals::get_rpd_kims($id);
        $lk = $podrazdel['raschitano_chasov_lekcyj'];
        $pr = $podrazdel['raschitano_chasov_praktik'];
        $vsego_chasov = $lk+$pr;

        $pdf = (new Pdf)->api;
        $pdf->title = $podrazdel['nazvanie'];

        $pdf->WriteHTML('<style>
                body {font-family: "Times New Roman", Times, serif;font-size: 10pt;}
                p.myp {text-indent: 2.5em;text-align: justify}
                .indent0 {text-indent: 2.5em;text-align: justify;margin:0}
                .center {text-align:center}
                .tb {border-collapse: collapse}
                .tb td {padding: 5px;border: 1px solid #000}
                .razdel-tr td {font-weight: bold}
                .attestatsiya-tr td {font-weight: bold}
                .bold-tr td {font-weight: bold}
                .bold {font-weight: bold}
                </style>');

        $pdf->WriteHTML('<p style="text-align:center">Министерство образования и науки Республики Бурятия</p>');
        $pdf->WriteHTML('<p style="text-align:center">ГАУ ДПО РБ «Бурятский республиканский институт образовательной политики»</p>');
        $pdf->WriteHTML('<p style="text-align:center">'.ApiGlobals::first_letter_up($kurs_info['podrazdelenie']).'</p>');

        $pdf->WriteHTML('<div style="text-align:right">УТВЕРЖДАЮ</div>');
        $pdf->WriteHTML('<div style="text-align:right">Проректор по организации</div>');
        $pdf->WriteHTML('<div style="text-align:right">образовательной деятельности</div>');
        $pdf->WriteHTML('<div style="text-align:right">_____________ / ______________</div>');
        $pdf->WriteHTML('<div style="text-align:right">« ____» __________ 20__ г.</div>');

        $pdf->WriteHTML('<div style="position: absolute;top: 40%;left:0;width:100%">
            <p class="center">РАБОЧАЯ ПРОГРАММА</p>
            <p  class="center">учебной дисциплины</p>
            '.$this->get_empty_row(1).'
            <p class="center bold">«'.$podrazdel['nazvanie'].'»</p>
            '.$this->get_empty_row(1));
        $pdf->WriteHTML('<p style="text-align:center;">'.($kurs_info['kurs_tip'] == 'po' ? 'основной профессиональной программы' : 'дополнительной профессиональной программы').'</p>');
        $pdf->WriteHTML('<p class="center">'.$tip.'</p>
            <div style="text-align:center;width: 100%;">
                <p style="width: 60%;margin: 0 auto">«'.mb_strtoupper($kurs_info['kurs_nazvanie'],'UTF-8').'»</p>
            </div>
            </div>
        ');

        $pdf->WriteHTML('<htmlpagefooter show-this-page="1" name="first_page_footer">
                         <div style="text-align: center"><p>Улан-Удэ</p><p>'.date('Y').' год</p></div>
                        </htmlpagefooter>');
        $pdf->SetHTMLFooterByName('first_page_footer');
        $pdf->AddPage();

        $pdf->WriteHTML('<p>Структурное подразделение: '.ApiGlobals::first_letter_up($kurs_info['podrazdelenie']).'</p>');
        $pdf->WriteHTML('<p>Составитель: '.$rukovoditel['familiya'].' '.
            ApiGlobals::get_first_letter($rukovoditel['imya']).'. '.
            ApiGlobals::get_first_letter($rukovoditel['otchestvo']).
            '., '.$rukovoditel['dolzhnost'].
            '.</p>');
        $pdf->WriteHTML($this->get_empty_row(2));
        $pdf->WriteHTML('<p>Обсуждена на заседании '.
            ($kurs_info['podrazdelenie_sokrashennoe_nazvanie'] ?
                $kurs_info['podrazdelenie_sokrashennoe_nazvanie'] :
                $kurs_info['podrazdelenie']).'.</p>');
        $pdf->WriteHTML('<p>Протокол № ___ от «__» _________ 20__ г.</p>');
        $pdf->WriteHTML($this->get_empty_row(2));
        $pdf->WriteHTML('<p>Утверждена на заседании НМС.</p>');
        $pdf->WriteHTML('<p>Протокол № ___ от «__» _________ 20__ г.</p>');
        $pdf->WriteHTML('<htmlpagefooter show-this-page="1" name="numbered_page_footer">
                         <div style="text-align: center">{PAGENO}</div>
                        </htmlpagefooter>');
        $pdf->SetHTMLFooterByName('numbered_page_footer');

        $pdf->AddPage();
        $pdf->WriteHTML('<p style="text-align: center"><b>Пояснительная записка</b></p>');
        $pdf->WriteHTML($this->get_paragraph('<b>Актуальность</b>'));
        $pdf->WriteHTML(ApiGlobals::parse_text($podrazdel['aktualnost']));
        $pdf->WriteHTML($this->get_paragraph('<b>Цель: </b>'.$podrazdel['cel']));
        $pdf->WriteHTML($this->get_paragraph('<b>Задачи:</b>'));
        $pdf->WriteHTML(ApiGlobals::parse_text($podrazdel['zadachi']));
        $pdf->WriteHTML($this->get_paragraph('<b>Планируемые результаты:</b>'));
        $pdf->WriteHTML(ApiGlobals::parse_text($podrazdel['planiruemye_rezultaty']));
        $pdf->WriteHTML($this->get_paragraph('<b>Место дисциплины в структуре программы: </b>'.$podrazdel['mesto_discipliny_v_strukture_programmy']));
        $pdf->WriteHTML($this->get_paragraph('<b>Количество часов: аудиторных '.$vsego_chasov.' ч.,</b> из них '.$lk.' ч. лекционных, '.$pr.' ч. практических.'));
        $pdf->WriteHTML($this->get_paragraph('<b>Организационно-педагогические условия:</b>'));
        $pdf->WriteHTML($this->get_paragraph('<i>Информационные:</i> '.$podrazdel['informacionnye_usloviya']));
        $pdf->WriteHTML($this->get_paragraph('<i>Учебно-методические:</i> '.$podrazdel['uchebnometodicheskie_usloviya']));
        $pdf->WriteHTML($this->get_paragraph('<i>Кадровые:</i> '.$podrazdel['kadrovye_usloviya']));
        $pdf->WriteHTML($this->get_paragraph('<i>Материально-технические:</i> '.$podrazdel['materialnotehnicheskie_usloviya']));
        $pdf->AddPage();
        $pdf->WriteHTML('<div class="center">Министерство образования и науки Республики Бурятия</div>');
        $pdf->WriteHTML('<div class="center">ГАУ ДПО РБ «Бурятский республиканский институт образовательной политики»</div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML('<div class="" style="text-align:right">УТВЕРЖДАЮ</div>');
        $pdf->WriteHTML('<div style="text-align:right">Проректор по организации</div>');
        $pdf->WriteHTML('<div style="text-align:right">образовательной деятельности</div>');
        $pdf->WriteHTML('<div style="text-align:right">_____________ / ______________</div>');
        $pdf->WriteHTML('<div class="lh1" style="text-align:right">« ____» __________ 20__ г.</div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML('<div class="center"><b>УЧЕБНЫЙ ПЛАН</b></div>');
        $pdf->WriteHTML('<div class="center">учебной дисциплины «'.$podrazdel['nazvanie'].'»</div>');
        if ($kurs_info['kurs_tip'] == 'pp') {
            $pdf->WriteHTML('<div class="center">дополнительной профессиональной программы</div>');
            $pdf->WriteHTML('<div class="center">профессиональной переподготовки</div>');
        }
        if ($kurs_info['kurs_tip'] == 'po') {
            $pdf->WriteHTML('<div class="center">основной профессиональной программы</div>');
            $pdf->WriteHTML('<div class="center">профессионального обучения</div>');
        }
        $pdf->WriteHTML('<div class="center">«'.$kurs_info['kurs_nazvanie'].'»</div>');
        $pdf->WriteHTML('<br>');
        //if ($kurs_info['kurs_tip'] == 'po') {
        $pdf->WriteHTML('<div class="left">Категория слушателей: ' . $kurs_info['kategorii'] . '</div>');
        $pdf->WriteHTML('<div class="left">Форма обучения: ' . $kurs_info['forma_obucheniya'] . '</div>');
        //}
        $pdf->WriteHTML('<div class="left">Режим занятий: ' . $kurs_info['rezhim_zanyatij'] . '</div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML($plan_html);
        $pdf->WriteHTML('<p style="text-align:left;">Руководитель курсов: ____________/'.ApiGlobals::get_first_letter($kurs_info['rukovoditel_imya']).'.'.ApiGlobals::get_first_letter($kurs_info['rukovoditel_otchestvo']).'. '.$kurs_info['rukovoditel_familiya'].'</p>');
        $pdf->WriteHTML('<p style="text-align:left;">Руководитель структурного подразделения: __________/ '.ApiGlobals::get_first_letter($rukovoditel_podrazdeleniya['imya']).'.'.ApiGlobals::get_first_letter($rukovoditel_podrazdeleniya['otchestvo']).'. '.$rukovoditel_podrazdeleniya['familiya'].'</p>');
        $pdf->WriteHTML('<p style="text-align:left;">Начальник учебного отдела: ___________/ Л.Е. Халудорова</p>');
        $pdf->AddPage();
        $pdf->WriteHTML('<div class="center">Министерство образования и науки Республики Бурятия</div>');
        $pdf->WriteHTML('<div class="center">ГАУ ДПО РБ «Бурятский республиканский институт образовательной политики»</div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML('<div class="" style="text-align:right">УТВЕРЖДАЮ</div>');
        $pdf->WriteHTML('<div style="text-align:right">Проректор по организации</div>');
        $pdf->WriteHTML('<div style="text-align:right">образовательной деятельности</div>');
        $pdf->WriteHTML('<div style="text-align:right">_____________ / ______________</div>');
        $pdf->WriteHTML('<div class="lh1" style="text-align:right">« ____» __________ 20__ г.</div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML('<div class="center"><b>КАЛЕНДАРНЫЙ УЧЕБНЫЙ ГРАФИК</b></div>');
        $pdf->WriteHTML('<div class="center">учебной дисциплины «'.$podrazdel['nazvanie'].'»</div>');
        if ($kurs_info['kurs_tip'] == 'pp') {
            $pdf->WriteHTML('<div class="center">дополнительной профессиональной программы</div>');
            $pdf->WriteHTML('<div class="center">профессиональной переподготовки</div>');
        }
        if ($kurs_info['kurs_tip'] == 'po') {
            $pdf->WriteHTML('<div class="center">основной профессиональной программы</div>');
            $pdf->WriteHTML('<div class="center">профессионального обучения</div>');
        }
        $pdf->WriteHTML('<div class="center">«'.$kurs_info['kurs_nazvanie'].'»</div>');

//        $pdf->WriteHTML('<div class="lh1" style="text-align:center;width: 100%;">
//                            <p style="font-weight:bold;width: 60%;margin: 0 auto;text-align: center">«'.$kurs['nazvanie'].'»</p>
//                        </div>');
        $pdf->WriteHTML('<br>');
        $pdf->WriteHTML($kug_html);
        $pdf->WriteHTML('<p style="text-align:left;">Руководитель курсов: ____________/'.ApiGlobals::get_first_letter($kurs_info['rukovoditel_imya']).'.'.ApiGlobals::get_first_letter($kurs_info['rukovoditel_otchestvo']).'. '.$kurs_info['rukovoditel_familiya'].'</p>');
        $pdf->WriteHTML('<p style="text-align:left;">Руководитель структурного подразделения: __________/ '.ApiGlobals::get_first_letter($rukovoditel_podrazdeleniya['imya']).'.'.ApiGlobals::get_first_letter($rukovoditel_podrazdeleniya['otchestvo']).'. '.$rukovoditel_podrazdeleniya['familiya'].'</p>');
        $pdf->WriteHTML('<p style="text-align:left;">Начальник учебного отдела: ___________/ Л.Е. Халудорова</p>');


        $pdf->AddPage();
        $pdf->WriteHTML($this->get_paragraph('Содержание','style="font-weight:bold;text-align:center"'));
        $pdf->WriteHTML(RpdGlobals::get_rpd_soderzhanie_html($soderzhanie,$nomer));
//var_dump($kims);die();
        if ($kims) {
            $pdf->AddPage();
            $pdf->WriteHTML('<div class="center"><b>КОНТРОЛЬНО-ИЗМЕРИТЕЛЬНЫЕ МАТЕРИАЛЫ</b></div>');
            foreach ($kims as $item) {
                if ($item['type'] == 1 or $item['type'] == 3)
                    $pdf->WriteHTML(RpdGlobals::get_rpd_kim_list_item($item));
            }
        }

        $pdf->AddPage();
        $pdf->WriteHTML($this->get_paragraph('Литература','style="font-weight:bold;text-align:center"'));
        $pdf->WriteHTML(ApiGlobals::parse_text($podrazdel['literatura']));

        $pdf->output();
        die();
    }

    public function get_empty_row($num=1){
        $html = '';
        for ($i=1;$i<=$num;$i++){
            $html .= '<p>&nbsp;</p>';
        }
        return $html;
    }

    public function get_paragraph($text = '&nbsp;',$attr=''){
        return '<p class="myp" '.$attr.'>'.$text.'</p>';
    }

}