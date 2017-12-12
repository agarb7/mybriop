<?php

 namespace app\modules\attestaciya_otchety\controllers;

use app\components\JsResponse;
use app\entities\AttestacionnoeVariativnoeIspytanie_3;
use app\entities\Dolzhnost;
use app\entities\DolzhnostAttestacionnojKomissii;
use app\entities\OtsenochnyjListZayavleniya;
use app\entities\PostoyannoeIspytanie;
use app\entities\VremyaProvedeniyaAttestacii;
use app\entities\ZayavlenieNaAttestaciyu;
use app\models\rukovoditel_attestacionnoj_komissii\Zayavlenie;
use app\modules\attestaciya_otchety\models\AttestaciyaItogovyjOtchet;
use kartik\mpdf\Pdf;
use app\enums\KategoriyaPedRabotnika;
use PHPExcel;
use PHPExcel_Writer_Excel5;
use yii\web\Response;
use Yii;

class ListController extends \app\components\Controller
{

    public function accessRules()
    {
        return [
          '*' => '@'
        ];
    }

    private function getPdfSeetings($content){
        $result = [];
        $indent = 3;
        $css = '
                body{
                   font-family:"Times New Roman","serif";
                }
                .paragraph{
                    text-align:justify;
                    margin-bottom: 5px;
                    margin-top: 5px;
                }
                .center{
                 text-align:center;
                }
                .tb {border-collapse: collapse}
                .tb td {padding: 5px;border: 1px solid #000}
                .inline-block{
                    display: inline-block;
                }
                .indent{padding-left: ' . $indent . 'em}

                .double-indent{padding-left: ' . (2 * $indent) . 'em}

                .indent-block{
                    margin-left: ' . $indent . 'em;
                }
                .bold{
                    font-weight: bold;
                }
                ';

        $result = [
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => $css,
            // set mPDF properties on the fly
            'options' => ['title' => 'Форма для печати'],
            // call mPDF methods on the fly
            'methods' => [
                //'SetHeader'=>['Krajee Report Header'],
                'SetFooter' => [''],
            ]
        ];
        return $result;
    }

    public function actionItogovyj()
    {
        if (isset($_GET['vp']) and $vremya_provedeniya = $_GET['vp'] and isset($_GET['d']) and $dolzhnost = $_GET['d']) {
            $data = \Yii::$app->db
                ->createCommand('select *
                             from attestaciya_itogovij_otchet(:vp,:d)
                             order by  case when otraslevoe_soglashenie is null then 0 else 1 end desc,
                              na_kategoriyu DESC,
                              imeushayasya_kategoriya DESC,
                              attestaciya_data_prisvoeniya DESC,
                              fio')
                ->bindValue(':vp',$vremya_provedeniya)
                ->bindValue(':d', $dolzhnost)
                ->queryAll();
            $groups = [
                'otraslevoe_soglashenie' => []
            ];
            foreach ($data as $item) {
                if ($item['na_kategoriyu'] == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA
                    and $item['otraslevoe_soglashenie']
                ) {
                    $groups['otraslevoe_soglashenie'][] = $item;
                } else {
                    $groups[$item['na_kategoriyu']][] = $item;
                }
            }
            $data = $groups;

            $excel = new PHPExcel();
            $excel->createSheet();
            $WorkSheet = $excel->getSheet(0);
            $WorkSheet->setTitle('Итоговый отчет');
            $WorkSheet->setCellValue('A1', 'Итоговый отчет');
            $WorkSheet->mergeCells('A1:M1'); /*Объединяем ячейки*/
            $WorkSheet->setCellValue('A3','№');
            $WorkSheet->setCellValue('B3','ФИО');
            $WorkSheet->setCellValue('C3','ОУ');
            $WorkSheet->setCellValue('D3','Должность');
            $WorkSheet->setCellValue('E3','Дата рождения');
            $WorkSheet->setCellValue('F3','Имеющаяся кв. кат.');
            $WorkSheet->setCellValue('G3','Стаж пед./вучр./в долж.');
            $WorkSheet->setCellValue('H3','Образование');
            $WorkSheet->setCellValue('I3','Повышение квалификации');
            $WorkSheet->setCellValue('J3','Рез-ты кв. экз');
            $WorkSheet->setCellValue('K3','Портфолио');
            $WorkSheet->setCellValue('L3','СПД');
            $WorkSheet->setCellValue('M3','Экспертное заключение');
            //$WorkSheet->getColumnDimension('K')->setWidth(30); /*ширина столбца (от руки)*/
            $WorkSheet->getStyle('A3:M3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $WorkSheet->getStyle('A3:M3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
            $WorkSheet->getStyle('A3:M3')->getFill()->getStartColor()->setRGB('bfbfbf');
            $excel->setActiveSheetIndex(0);
            foreach(range('A', 'M') as $columnId){
                $excel->getActiveSheet()->getColumnDimension($columnId)->setAutoSize(true);
            }
            $number = 1;
            $current_kategoriya = '';
            $row_number = 4;
            foreach ($data as $key => $items){
                if ($current_kategoriya != $key and $items){
                    $kategoriya = '';
                    if ($key == 'otraslevoe_soglashenie'){
                        $kategoriya =  'Высшая категория (по отраслевому соглашению)';
                    }
                    else {
                        $kategoriya = \app\globals\ApiGlobals::first_letter_up(KategoriyaPedRabotnika::namesMap()[$key]);
                    }

                    $WorkSheet->setCellValue('A'.$row_number, $kategoriya);
                    $WorkSheet->mergeCells('A'.$row_number.':M'.$row_number);
                    $WorkSheet->getStyle('A'.$row_number.':M'.$row_number)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $current_kategoriya = $key;
                    $number = 1;
                    $row_number++;
                }
                foreach ($items as $item) {


                    $WorkSheet->setCellValue('A' . $row_number, $number);
                    $WorkSheet->setCellValue('B' . $row_number, $item['fio']);
                    $WorkSheet->setCellValue('C' . $row_number, $item['organizaciya']);
                    $WorkSheet->setCellValue('D' . $row_number, $item['dolzhnost']);
                    $WorkSheet->setCellValue('E' . $row_number, date('d.m.Y', strtotime($item['data_rozhdeniya'])));
                    $WorkSheet->setCellValue('F' . $row_number, KategoriyaPedRabotnika::namesMap()[$item['imeushayasya_kategoriya']] .
                        ($item['attestaciya_data_okonchaniya_dejstviya'] != '1970-01-01' ? ', ' . date('d.m.Y', strtotime($item['attestaciya_data_okonchaniya_dejstviya'])) : ''));
                    $WorkSheet->setCellValue('G' . $row_number, $item['ped_stazh'] . '/' . $item['rabota_stazh_v_dolzhnosti'] . '/' . $item['stazh_v_dolzhnosti']);
                    $WorkSheet->setCellValue('H' . $row_number, $item['obrazovanie']);
                    $WorkSheet->setCellValue('I' . $row_number, $item['kursy']);
                    $var_isp = '';
                    if ($item['na_kategoriyu'] == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA) {
                        $var_isp = 'Не предусмотрена';
                    } else {
                        if ($item['otraslevoe_soglashenie']) {
                            $var_isp = $item['otraslevoe_soglashenie'];
                        } else {
                            $var_isp = number_format($item['variativnoe_ispytanie_3'], 2);
                        }
                    }
                    $WorkSheet->setCellValue('J' . $row_number, $var_isp);
                    $WorkSheet->setCellValue('K' . $row_number, number_format($item['portfolio'], 2));
                    $WorkSheet->setCellValue('L' . $row_number, (
                        $item['na_kategoriyu'] == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA or $item['otraslevoe_soglashenie'])
                        ? 'Не предусмотрена'
                        : number_format($item['spd'], 2));
                    $WorkSheet->setCellValue('M' . $row_number, $item['count_below'] == 0 ? 'Рекомендовано' : 'Не рекомендовано');
                    $number++;
                    $row_number++;
                }

            }
            header("Expires: Mon,1 Apr 1974 05:00:00 GMT");
            header("Last-Modified: ".gmdate("D,d M YH:i:s")." GMT");
            header("Cache-Control: no-cache,must-revalidate");
            header("Pragma: no-cache");
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=report.xls");
            $objWriter=new PHPExcel_Writer_Excel5($excel); /*Выводим содержимое файла*/
            $objWriter->save('php://output');
            die();

            $content = $this->renderPartial('itogovyj-report', compact('data'));
            $indent = 3;
            $css = '
                body{
                   font-family:"Times New Roman","serif";
                }
                .paragraph{
                    text-align:justify;
                    margin-bottom: 5px;
                    margin-top: 5px;
                }
                .center{
                 text-align:center;
                }
                .tb {border-collapse: collapse}
                .tb td {padding: 5px;border: 1px solid #000}
                .inline-block{
                    display: inline-block;
                }
                .indent{padding-left: ' . $indent . 'em}

                .double-indent{padding-left: ' . (2 * $indent) . 'em}

                .indent-block{
                    margin-left: ' . $indent . 'em;
                }
                .bold{
                    font-weight: bold;
                }
                ';

            $pdf = new Pdf([
                // set to use core fonts only
                'mode' => Pdf::MODE_UTF8,
                // A4 paper format
                'format' => Pdf::FORMAT_A4,
                // portrait orientation
                'orientation' => Pdf::ORIENT_LANDSCAPE,
                // stream to browser inline
                'destination' => Pdf::DEST_BROWSER,
                // your html content input
                'content' => $content,
                // format content from your own css file if needed or use the
                // enhanced bootstrap css built by Krajee for mPDF formatting
                'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
                // any css to be embedded if required
                'cssInline' => $css,
                // set mPDF properties on the fly
                'options' => ['title' => 'Отчет для к заседанию комиссии'],
                // call mPDF methods on the fly
                'methods' => [
                    //'SetHeader'=>['Krajee Report Header'],
                    'SetFooter' => [''],
                ]
            ]);
            // return the pdf output as per the destination setting
            return $pdf->render();
        }
        else{
            $dolzhnosti = Dolzhnost::getDolzhnostiAttestacii();
            return $this->render('itogovyj', compact('dolzhnosti'));
        }
    }

    public function actionOtsenochnyjList()
    {
        $request = \Yii::$app->request;
        $type = $request->get('type','');
        $id = $request->get('id', 0);
        $zid = $request->get('zid', 0);
        $zayavlenie = ZayavlenieNaAttestaciyu::findOne($zid);
        $query = OtsenochnyjListZayavleniya::find()
            ->joinWith('strukturaOtsenochnogoListaZayvaleniyaRel')
            ->joinWith('rabotnikKomissiiFizLicoRel')
            ->orderBy('fiz_lico.familiya, fiz_lico.imya, fiz_lico.otchestvo')
            ->where(['otsenochnyj_list_zayavleniya.zayavlenie_na_attestaciyu' => $zid]);
        $ispytanie = [];
        if ($type == 'postoyannoe'){
            $query = $query->andWhere(['otsenochnyj_list_zayavleniya.postoyannoe_ispytanie' => $id]);
            $ispytanie = PostoyannoeIspytanie::findOne($id);
        }
        elseif ($type == 'variativnoe'){
            $query = $query->andWhere(['otsenochnyj_list_zayavleniya.var_ispytanie_3' => $id]);
            $ispytanie = AttestacionnoeVariativnoeIspytanie_3::findOne($id);
        }
        $data = $query->all();
        $content = $this->renderPartial('otsenochnyj-list', compact('data','zayavlenie','ispytanie'));


        $pdf = new Pdf($this->getPdfSeetings($content));
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionVarIsp()
    {
        if (isset($_GET['vp']) and $vremya_provedeniya = $_GET['vp']) {
            $ispytaniya = AttestacionnoeVariativnoeIspytanie_3::find()->orderBy('nazvanie')->all();
            $sql = "SELECT
                  t.var_ispytanie_3, t.dolzhnost_id, t.dolzhnost_nazvanie,
                  SUM(CASE WHEN t.count_bally_below_min = 0 THEN 1 ELSE 0 end) as reccomended,
                  SUM(CASE WHEN t.count_bally_below_min > 0 THEN 1 ELSE 0 end) as notreccomended
                FROM
                (
                  SELECT
                    z.id as zayavlenie_id, z.var_ispytanie_3, d.id as dolzhnost_id, d.nazvanie as dolzhnost_nazvanie,
                    count_bally_below_min(z.id)
                  FROM zayavlenie_na_attestaciyu AS z
                    INNER JOIN dolzhnost AS d ON z.rabota_dolzhnost = d.id
                  WHERE z.status = 'podpisano_otdelom_attestacii' AND z.vremya_provedeniya = :vp AND z.var_ispytanie_3 IS NOT NULL
                ) AS t
                GROUP BY t.var_ispytanie_3, t.dolzhnost_id, t.dolzhnost_nazvanie
                ORDER BY t.dolzhnost_nazvanie";
            $data = \Yii::$app->db->createCommand($sql)->bindValue(':vp', $vremya_provedeniya)->queryAll();
            $report = [];
            foreach ($data as $item) {
                if (!isset($report[$item['dolzhnost_id']])) {
                    $report[$item['dolzhnost_id']] = [
                        'dolzhnost_nazvanie' => $item['dolzhnost_nazvanie'],
                        'var_isp' => []
                    ];
                }
                $report[$item['dolzhnost_id']]['var_isp'][$item['var_ispytanie_3']] = [
                    'reccomended' => $item['reccomended'],
                    'notreccomended' => $item['notreccomended']
                ];
            };
            $vremya = VremyaProvedeniyaAttestacii::findOne($vremya_provedeniya);
            $content =  $this->renderPartial('var-isp.php', compact('vremya', 'report', 'ispytaniya'));

            $pdf = new Pdf($this->getPdfSeetings($content));
            // return the pdf output as per the destination setting
            return $pdf->render();
        }
        else{
            $dolzhnosti = Dolzhnost::getDolzhnostiAttestacii();
            return $this->render('var-isp-form.php');
        }
    }

    public function actionOtchetByDolzhnost()
    {
        if (isset($_GET['vp']) and $vremya_provedeniya = $_GET['vp']) {
            $ispytaniya = AttestacionnoeVariativnoeIspytanie_3::find()->orderBy('nazvanie')->all();
            $sql = "SELECT
                  t.dolzhnost_id, t.dolzhnost_nazvanie,
                  EXTRACT(YEAR from t.nachalo) as year,
                  EXTRACT(month from t.nachalo) as month,
                  SUM(CASE WHEN t.na_kategoriyu = 'pervaya_kategoriya' and t.count_bally_below_min = 0 THEN 1 ELSE 0 end) as pervaya_reccomended,
                  SUM(CASE WHEN t.na_kategoriyu = 'pervaya_kategoriya' and t.count_bally_below_min > 0 THEN 1 ELSE 0 end) as pervaya_notreccomended,
                  SUM(CASE WHEN t.na_kategoriyu = 'vyshaya_kategoriya' and t.count_bally_below_min = 0 THEN 1 ELSE 0 end) as vyshaya_reccomended,
                  SUM(CASE WHEN t.na_kategoriyu = 'vyshaya_kategoriya' and t.count_bally_below_min > 0 THEN 1 ELSE 0 end) as vyshaya_notreccomended,
                  count(*) as all_zayavleniya
                FROM
                (
                  SELECT
                    z.id as zayavlenie_id, d.id as dolzhnost_id, d.nazvanie as dolzhnost_nazvanie,
                    count_bally_below_min(z.id),
                    vpa.nachalo, z.na_kategoriyu
                  FROM zayavlenie_na_attestaciyu AS z
                    INNER JOIN dolzhnost AS d ON z.rabota_dolzhnost = d.id
                    INNER JOIN vremya_provedeniya_attestacii as vpa on z.vremya_provedeniya = vpa.id
                  WHERE z.status = 'podpisano_otdelom_attestacii' AND z.vremya_provedeniya = :vp --AND z.var_ispytanie_3 IS NOT NULL
                ) AS t
                GROUP BY t.nachalo, t.dolzhnost_id, t.dolzhnost_nazvanie
                ORDER BY t.dolzhnost_nazvanie";
            $data = \Yii::$app->db->createCommand($sql)->bindValue(':vp', $vremya_provedeniya)->queryAll();
            $report = [];
            foreach ($data as $item) {
                $report[$item['dolzhnost_id']] = $item;
            };
            $vremya = VremyaProvedeniyaAttestacii::findOne($vremya_provedeniya);
            $content =  $this->renderPartial('dolzhnost.php', compact('vremya', 'report', 'ispytaniya'));

            $pdf = new Pdf($this->getPdfSeetings($content));
            // return the pdf output as per the destination setting
            return $pdf->render();
        }
        else{
            return $this->render('dolzhnost-form.php');
        }
    }

    public function actionOtchetByRajon(){
        if (isset($_GET['vp']) and $vremya_provedeniya = $_GET['vp']) {
            $ispytaniya = AttestacionnoeVariativnoeIspytanie_3::find()->orderBy('nazvanie')->all();
            $sql = "SELECT
                      t.rajon_id,
                      t.rajon,
                      EXTRACT(YEAR from t.nachalo) as year,
                      EXTRACT(month from t.nachalo) as month,
                      SUM(CASE WHEN t.na_kategoriyu = 'pervaya_kategoriya' and t.count_bally_below_min = 0 THEN 1 ELSE 0 end) as pervaya_reccomended,
                      SUM(CASE WHEN t.na_kategoriyu = 'pervaya_kategoriya' and t.count_bally_below_min > 0 THEN 1 ELSE 0 end) as pervaya_notreccomended,
                      SUM(CASE WHEN t.na_kategoriyu = 'vyshaya_kategoriya' and t.count_bally_below_min = 0 THEN 1 ELSE 0 end) as vyshaya_reccomended,
                      SUM(CASE WHEN t.na_kategoriyu = 'vyshaya_kategoriya' and t.count_bally_below_min > 0 THEN 1 ELSE 0 end) as vyshaya_notreccomended,
                      count(*) as all_zayavleniya
                    FROM
                      (
                        SELECT
                          z.id as zayavlenie_id,
                          count_bally_below_min(z.id),
                          vpa.nachalo, z.na_kategoriyu,
                          coalesce(ao.formalnoe_nazvanie, 'не задано') as rajon,
                          COALESCE(ao.id, -1) as rajon_id
                        FROM zayavlenie_na_attestaciyu AS z
                          INNER JOIN organizaciya as o on z.rabota_organizaciya = o.id
                          left JOIN adresnyj_objekt as ao on o.adres_adresnyj_objekt = ao.id
                          INNER JOIN vremya_provedeniya_attestacii as vpa on z.vremya_provedeniya = vpa.id
                        WHERE z.status = 'podpisano_otdelom_attestacii' AND z.vremya_provedeniya = :vp
                      ) AS t
                    GROUP BY t.nachalo, t.rajon, t.rajon_id
                    ORDER BY t.rajon";
            $data = \Yii::$app->db->createCommand($sql)->bindValue(':vp', $vremya_provedeniya)->queryAll();
            $report = [];
            foreach ($data as $item) {
                $report[$item['rajon_id']] = $item;
            };
            $vremya = VremyaProvedeniyaAttestacii::findOne($vremya_provedeniya);
            $content =  $this->renderPartial('rajon', compact('vremya', 'report', 'ispytaniya'));

            $pdf = new Pdf($this->getPdfSeetings($content));
            // return the pdf output as per the destination setting
            return $pdf->render();
        }
        else{
            return $this->render('rajon-form');
        }
    }


    public function actionItogovyjByKomissiya(){
        $komissiya = $_GET['komissiya'];
        $period = $_GET['period'];
        $posts = DolzhnostAttestacionnojKomissii::find()
            ->where(['attestacionnaya_komissiya' => $komissiya])
            ->distinct('dolzhnost')
            ->select('dolzhnost')
            ->all();
        $data = [];
        foreach ($posts as $post) {
            $query = \Yii::$app->db
                ->createCommand('select *
                             from attestaciya_itogovij_otchet(:vp,:d)
                             order by  case when otraslevoe_soglashenie is null then 0 else 1 end desc,
                              na_kategoriyu DESC,
                              imeushayasya_kategoriya DESC,
                              attestaciya_data_prisvoeniya DESC,
                              fio')
                ->bindValue(':vp',$period)
                ->bindValue(':d', $post->dolzhnost)
                ->queryAll();
            $data = array_merge($data, $query);
        }
        //var_dump($data);die();

        $groups = [
            'otraslevoe_soglashenie' => []
        ];
        foreach ($data as $item) {
            if ($item['na_kategoriyu'] == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA
                and $item['otraslevoe_soglashenie']
            ) {
                $groups['otraslevoe_soglashenie'][] = $item;
            } else {
                $groups[$item['na_kategoriyu']][] = $item;
            }
        }
        $data = $groups;

        $excel = new PHPExcel();
        $excel->createSheet();
        $WorkSheet = $excel->getSheet(0);
        $WorkSheet->setTitle('Итоговый отчет');
        $WorkSheet->setCellValue('A1', 'Итоговый отчет');
        $WorkSheet->mergeCells('A1:M1'); /*Объединяем ячейки*/
        $WorkSheet->setCellValue('A3','№');
        $WorkSheet->setCellValue('B3','ФИО');
        $WorkSheet->setCellValue('C3','ОУ');
        $WorkSheet->setCellValue('D3','Должность');
        $WorkSheet->setCellValue('E3','Дата рождения');
        $WorkSheet->setCellValue('F3','Имеющаяся кв. кат.');
        $WorkSheet->setCellValue('G3','Стаж пед./вучр./в долж.');
        $WorkSheet->setCellValue('H3','Образование');
        $WorkSheet->setCellValue('I3','Повышение квалификации');
        $WorkSheet->setCellValue('J3','Рез-ты кв. экз');
        $WorkSheet->setCellValue('K3','Портфолио');
        $WorkSheet->setCellValue('L3','СПД');
        $WorkSheet->setCellValue('M3','Экспертное заключение');
        //$WorkSheet->getColumnDimension('K')->setWidth(30); /*ширина столбца (от руки)*/
        $WorkSheet->getStyle('A3:M3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $WorkSheet->getStyle('A3:M3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $WorkSheet->getStyle('A3:M3')->getFill()->getStartColor()->setRGB('bfbfbf');
        $excel->setActiveSheetIndex(0);
        foreach(range('A', 'M') as $columnId){
            $excel->getActiveSheet()->getColumnDimension($columnId)->setAutoSize(true);
        }
        $number = 1;
        $current_kategoriya = '';
        $row_number = 4;
        foreach ($data as $key => $items){
            if ($current_kategoriya != $key and $items){
                $kategoriya = '';
                if ($key == 'otraslevoe_soglashenie'){
                    $kategoriya =  'Высшая категория (по отраслевому соглашению)';
                }
                else {
                    $kategoriya = \app\globals\ApiGlobals::first_letter_up(KategoriyaPedRabotnika::namesMap()[$key]);
                }

                $WorkSheet->setCellValue('A'.$row_number, $kategoriya);
                $WorkSheet->mergeCells('A'.$row_number.':M'.$row_number);
                $WorkSheet->getStyle('A'.$row_number.':M'.$row_number)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $current_kategoriya = $key;
                $number = 1;
                $row_number++;
            }
            foreach ($items as $item) {


                $WorkSheet->setCellValue('A' . $row_number, $number);
                $WorkSheet->setCellValue('B' . $row_number, $item['fio']);
                $WorkSheet->setCellValue('C' . $row_number, $item['organizaciya']);
                $WorkSheet->setCellValue('D' . $row_number, $item['dolzhnost']);
                $WorkSheet->setCellValue('E' . $row_number, date('d.m.Y', strtotime($item['data_rozhdeniya'])));
                $WorkSheet->setCellValue('F' . $row_number, KategoriyaPedRabotnika::namesMap()[$item['imeushayasya_kategoriya']] .
                    ($item['attestaciya_data_okonchaniya_dejstviya'] != '1970-01-01' ? ', ' . date('d.m.Y', strtotime($item['attestaciya_data_okonchaniya_dejstviya'])) : ''));
                $WorkSheet->setCellValue('G' . $row_number, $item['ped_stazh'] . '/' . $item['rabota_stazh_v_dolzhnosti'] . '/' . $item['stazh_v_dolzhnosti']);
                $WorkSheet->setCellValue('H' . $row_number, $item['obrazovanie']);
                $WorkSheet->setCellValue('I' . $row_number, $item['kursy']);
                $var_isp = '';
                if ($item['na_kategoriyu'] == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA) {
                    $var_isp = 'Не предусмотрена';
                } else {
                    if ($item['otraslevoe_soglashenie']) {
                        $var_isp = $item['otraslevoe_soglashenie'];
                    } else {
                        $var_isp = number_format($item['variativnoe_ispytanie_3'], 2);
                    }
                }
                $WorkSheet->setCellValue('J' . $row_number, $var_isp);
                $WorkSheet->setCellValue('K' . $row_number, number_format($item['portfolio'], 2));
                $WorkSheet->setCellValue('L' . $row_number, (
                    $item['na_kategoriyu'] == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA or $item['otraslevoe_soglashenie'])
                    ? 'Не предусмотрена'
                    : number_format($item['spd'], 2));
                $WorkSheet->setCellValue('M' . $row_number, $item['count_below'] == 0 ? 'Рекомендовано' : 'Не рекомендовано');
                $number++;
                $row_number++;
            }

        }
        header("Expires: Mon,1 Apr 1974 05:00:00 GMT");
        header("Last-Modified: ".gmdate("D,d M YH:i:s")." GMT");
        header("Cache-Control: no-cache,must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=report.xls");
        $objWriter=new PHPExcel_Writer_Excel5($excel); /*Выводим содержимое файла*/
        $objWriter->save('php://output');
        die();

    }


    public function actionSotrudnikKomissii(){
        if (isset($_REQUEST['vp'])){
            $vp = $_REQUEST['vp'];
            $komissiya = $_REQUEST['komissiya'];

            $sql = 'select 
                       zna.id, fl.id as fiz_lico_id,
                       fl.familiya, fl.imya, fl.otchestvo,
                       sum(solz.bally) as bally
                    from zayavlenie_na_attestaciyu as zna
                      inner join otsenochnyj_list_zayavleniya as olz on zna.id = olz.zayavlenie_na_attestaciyu
                      inner join struktura_otsenochnogo_lista_zayvaleniya as solz on olz.id = solz.otsenochnyj_list_zayavleniya
                      inner join fiz_lico as fl on olz.rabotnik_komissii = fl.id 
                    where zna.vremya_provedeniya = :vp and solz.uroven = 1
                      and zna.rabota_dolzhnost in (
                          select dolzhnost from dolzhnost_attestacionnoj_komissii where attestacionnaya_komissiya = :ak
                        )
                    group by zna.id, fl.id, fl.familiya, fl.imya, fl.otchestvo    
                    HAVING sum(solz.bally) > 0
                    ';

            $data = \Yii::$app->db->createCommand($sql)
                ->bindValue(':vp', $vp)
                ->bindValue(':ak', $komissiya)
                ->queryAll();
            $sotrudniki = [];
            foreach ($data as $item) {
                if (!isset($sotrudniki[$item['fiz_lico_id']])){
                    $sotrudniki[$item['fiz_lico_id']] = $item;
                    $sotrudniki[$item['fiz_lico_id']]['count'] = 1;
                }
                else{
                    $sotrudniki[$item['fiz_lico_id']]['count']++;
                }
            }

            \Yii::$app->response->format = Response::FORMAT_JSON;
            $response = new JsResponse();
            $response->data = $sotrudniki;
            return $response;
        }
        else{
            return $this->render('sotrudnik-form');
        }
    }
    
    public function actionSpisokAttestuemyh($vp){
        $sql = "SELECT z.id as id, z.familiya as f, z.imya as i, z.otchestvo as o  
                    FROM zayavlenie_na_attestaciyu AS z
                    WHERE z.status = 'podpisano_otdelom_attestacii' AND z.vremya_provedeniya = :vp";
        $data =\Yii::$app->db->createCommand($sql)->bindValue(':vp', $vp)->queryAll();
        $countAttestuemyh = count($data);
        if($countAttestuemyh>0) {
            echo "<option>Выберите аттестуемого</option>";
            foreach ($data as $item) {
                echo "<option value='" . $item['id'] . "'>" . $item['f'] . ' ' . $item['i'] . ' ' . $item['o'] . "</option>";
            }
        }
        else{
                echo "<option>Нет подтвержденных заявлений</option>";
        }
    }

    public function actionOtchetByPortfolio(){
        if (isset($_GET['z']) and $zid = $_GET['z']) {
            $zayavlenie = ZayavlenieNaAttestaciyu::find()
                ->with('dolzhnostRel')
                ->with('organizaciyaRel')
                ->where(['id' => $zid])
                ->one();

            $list = OtsenochnyjListZayavleniya::find()
                ->joinWith('strukturaOtsenochnogoListaZayvaleniyaRel')
                ->joinWith('rabotnikKomissiiFizLicoRel')
                ->orderBy('fiz_lico.familiya, fiz_lico.imya, fiz_lico.otchestvo')
                ->where(['otsenochnyj_list_zayavleniya.zayavlenie_na_attestaciyu' => $zid])
                ->andWhere(['otsenochnyj_list_zayavleniya.postoyannoe_ispytanie' => PostoyannoeIspytanie::PORTFOLIO_ID])
                ->andWhere(['otsenochnyj_list_zayavleniya.otsenochnij_list' => 8])
                ->all();

            if(!empty($list)){
                $content = $this->renderPartial('portfolio', compact('list', 'zayavlenie'),true);
                $pdf = new Pdf($this->getPdfSeetings($content));
                return $pdf->render();
            }else{
                echo "Оценочные листы еще не заполнены!!!";
            }


        }
        else{
            return $this->render('portfolio-form');
        }
    }

    public function actionOtchetByIk(){
        if (isset($_GET['z']) and $zid = $_GET['z']) {
            $zayavlenie = ZayavlenieNaAttestaciyu::find()
                ->with('dolzhnostRel')
                ->with('organizaciyaRel')
                ->where(['id' => $zid])
                ->one();

            $list = OtsenochnyjListZayavleniya::find()
                ->joinWith('strukturaOtsenochnogoListaZayvaleniyaRel')
                ->joinWith('rabotnikKomissiiFizLicoRel')
                ->orderBy('fiz_lico.familiya, fiz_lico.imya, fiz_lico.otchestvo')
                ->where(['otsenochnyj_list_zayavleniya.zayavlenie_na_attestaciyu' => $zid])
                ->andWhere(['in','otsenochnyj_list_zayavleniya.postoyannoe_ispytanie', PostoyannoeIspytanie::getIkId()])
                ->orFilterWhere(['and',['otsenochnyj_list_zayavleniya.postoyannoe_ispytanie' => PostoyannoeIspytanie::PORTFOLIO_ID],
                    ['in','otsenochnyj_list_zayavleniya.otsenochnij_list',[12,13,14,16,17,18,19,20,21,22,24,26,27]]])
                ->all();

            if(!empty($list)){
                $content = $this->renderPartial('ik', compact('list', 'zayavlenie'),true);
                $pdf = new Pdf($this->getPdfSeetings($content));
                return $pdf->render();
            }else{
                echo "Оценочные листы еще не заполнены!!!";
            }


        }
        else{
            return $this->render('ik-form');
        }
    }
}