<?php
use \yii\helpers\Html;

$this->title = 'Аттестация';

$this->registerJsFile('/js/attIspytaniya.js');

$this->registerCss('
    .link-btn{
        background:#eee!important;
        border-radius:4px!important;
    }

    .link-btn{
        background:#eee!important;
        border-radius:4px!important;
    }

    .zayavlenie_fajl{
        width: 15em;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .zayavlenie_row{
        background:#eee;
        padding: 0.5em;
        margin-bottom: 10px;
    }

    .zayavlenie_row a{
        font-size: 1.1em;
    }

    .fajly_tb{
        margin-left: 2em;margin-bottom:1em;
    }

    .ball_row{
        padding: 0.5em;
    }

');

echo Html::a('Регистрация','/attestaciya/registraciya/',['class'=>'btn btn-primary']);

$kategorii = \app\enums\KategoriyaPedRabotnika::namesMap();

if (date('Y-m-d')<'2017-06-30') echo "<div style='margin-top: 10px'><p><b>Внимание!</b><br> 
    Уважаемые коллеги, оценивание педагогических работников по \"Портфолио\" отменяется с 1 сентября 2017г. 
    При прохождении аттестации до указанной даты по должностям не относящимся к должности \"Учитель\" Вы можете выбрать один из вариантов оценивания по Портфолио или Информационной карте. 
    Выбор варианта оценивания подтверждается загрузкой соответствующего файла.
</p></div>";
echo Html::tag('h3','Список заявлений');
?>

<?foreach ($list as $k=>$v) {?>
    <div class="zayavlenie_row">
        <?= Html::a('Заявление по должности "'.$v->dolzhnostRel['nazvanie'].'" на "' . $kategorii[$v->na_kategoriyu] . '" (начало аттестации ' . $v->vremyaProvedeniyaAttestaciiRel->nachalo . ')' .
            ($v->status == \app\enums\StatusZayavleniyaNaAttestaciyu::OTKLONENO ? ' (отклонено отделом аттестации для доработки)' : '') .
            ($v->status == \app\enums\StatusZayavleniyaNaAttestaciyu::ZABLOKIROVANO_OTDELOM_ATTESTACII ? ' (заблокировано отделом аттестации)' : '') .
            ($v->status == \app\enums\StatusZayavleniyaNaAttestaciyu::V_OTDELE_ATTESTACII ? ' (рассматривается отделом аттестации)' : '') .
            ($v->status == \app\enums\StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII ? ' (подписано отделом аттестации)' : ''),
            \yii\helpers\Url::to(['/attestaciya/registraciya/', 'zid' => $v->id])) ?>
    </div>
    <?if ($otsenki[$v->id]['rabotnik_count'] == $otsenki[$v->id]['podpisannie_otsenki_count'] && $v->status == \app\enums\StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII):?>
        <div class="ball_row text-info">
            Средний балл: <?=number_format($otsenki[$v->id]['avg_ball'],2)?>
        </div>
    <?endif?>
    <?if ($v->status == \app\enums\StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII and $v->rabota_dolzhnost != 47){?>
        <table class="tb fajly_tb">
            <? if ($v->vremyaProvedeniyaAttestaciiRel->nachalo < '2017-09-01') {?>
                <tr>
                    <td>Портфолио</td>
                    <td>-</td>
                    <td>
                        <div class="inline-block" id="portfolio<?=$v->id?>">
                            <?if ($v->portfolio) echo $v->portfolioFajlRel->getFileLink('zayavlenie_fajl btn btn-link link-btn')?>
                        </div>
                        <?=\app\widgets\Files2Widget::widget([
                            'select_callback'=>'select_portfolio_callback',
                            'caption' => $v->portfolio ? 'Изменить файл' : 'Выбрать файл',
                            'options'=>[
                                'data-zayavlenie-id'=>$v->id,
                                'style' => 'display:inline-block',
                            ],
                            'file_id' => isset($v->portfolioFajlRel->id) ? $v->portfolioFajlRel->id : -1
                        ])?>
                    </td>
                </tr>
            <?}?>
                <tr>
                    <td><?=($v->informacionnaja_karta)?'Информационная карта загружена':'Загрузите информационную карту'?></td>
                    <td></td>
                    <td>
                        <div class="inline-block" id="informacionnaja_karta<?=$v->id?>">
                            <?if ($v->informacionnaja_karta) echo $v->informacionnajaKartaFajlRel->getFileLink('zayavlenie_fajl btn btn-link link-btn')?>
                        </div>
                        <?=\app\widgets\Files2Widget::widget([
                            'select_callback'=>'select_ik_callback',
                            'caption' => $v->informacionnaja_karta ? 'Изменить файл' : 'Выбрать файл',
                            'options'=>[
                                'data-zayavlenie-id'=>$v->id,
                                'style' => 'display:inline-block',
                            ],
                            'file_id' => isset($v->informacionnajaKartaFajlRel->id) ? $v->informacionnajaKartaFajlRel->id : -1
                        ])?>
                    </td>
                </tr>
            <? if ($v->vremyaProvedeniyaAttestaciiRel->nachalo < '2017-12-01' and $v->na_kategoriyu == \app\enums\KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA and count($v->otraslevoeSoglashenieZayavleniyaRel) == 0): ?>
            <tr>
                <td><?= \app\globals\ApiGlobals::first_letter_up(
                        $v->attestacionnoeVariativnoeIspytanie3Rel['nazvanie']) ?>
                </td>
                <td>-</td>
                <td>
                    <div class="inline-block"  id="var_isp<?=$v->id?>">
                        <?
                            if (isset($v->varIspytanie2FajlRel) and $v->varIspytanie2FajlRel)
                                echo $v->varIspytanie2FajlRel->getFileLink('zayavlenie_fajl btn btn-link link-btn');
                            if (isset($v->varIspytanie3FajlRel) and $v->varIspytanie3FajlRel)
                                echo $v->varIspytanie3FajlRel->getFileLink('zayavlenie_fajl btn btn-link link-btn');
                        ?>
                    </div>
                    <?=\app\widgets\Files2Widget::widget([
                        'select_callback'=>$v->attestacionnoeVariativnoeIspytanie2Rel ?
                            'select_var_isp2_callback' :
                            'select_var_isp3_callback',
                        'options'=>[
                            'data-zayavlenie-id'=>$v->id,
                            'style' => 'display:inline-block'
                        ],
                        'caption' => (($v->var_ispytanie_2_fajl Or $v->var_ispytanie_3_fajl) ? 'Изменить файл' : 'Выбрать файл'),
                        'file_id' => $v->attestacionnoeVariativnoeIspytanie2Rel ?
                                     $v->var_ispytanie_2_fajl :
                                     $v->var_ispytanie_3_fajl
                    ])?>
                </td>
            </tr>
        <?//endif?>
        <?//if ($v->na_kategoriyu == \app\enums\KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA and date('Y-m-d')<'2017-10-13'): ?>
            <tr>
                <td>
                    СПД
                </td>
                <td>-</td>
                <td>
                    <div class="inline-block" id="prezentatsiya<?=$v->id?>">
                        <?
                            if ($v->prezentatsiya) echo $v->prezentatsiyaFajlRel->getFileLink('zayavlenie_fajl btn btn-link link-btn')
                        ?>
                    </div>
                    <?=\app\widgets\Files2Widget::widget([
                        'select_callback'=>'select_prezentatsiya',
                        'options'=>[
                            'data-zayavlenie-id'=>$v->id,
                            'style' => 'display:inline-block'
                        ],
                        'caption' => (($v->prezentatsiya) ? 'Изменить файл' : 'Выбрать файл'),
                        'file_id' => $v->prezentatsiyaFajlRel['id']
                    ])?>
                </td>
            </tr>
         <?endif?>
        </table>
    <?}?>
<?
}
?>


