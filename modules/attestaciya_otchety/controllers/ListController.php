<?php

 namespace app\modules\attestaciya_otchety\controllers;

use app\entities\Dolzhnost;
use app\modules\attestaciya_otchety\models\AttestaciyaItogovyjOtchet;
use kartik\mpdf\Pdf;
use app\enums\KategoriyaPedRabotnika;

class ListController extends \app\components\Controller
{

    public function accessRules()
    {
        return [
          '*' => '*'
        ];
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
}