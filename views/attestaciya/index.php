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

echo Html::tag('h3','Список заявлений');

//echo '<ul class="">';
?>

<?foreach ($list as $k=>$v) {?>
    <div class="zayavlenie_row">
        <?= Html::a('Заявление на "' . $kategorii[$v->na_kategoriyu] . '" (начало аттестации ' . $v->vremyaProvedeniyaAttestaciiRel->nachalo . ')' .
            ($v->status == \app\enums\StatusZayavleniyaNaAttestaciyu::OTKLONENO ? '(заявление отклонено)' : ''),
            \yii\helpers\Url::to(['/attestaciya/registraciya/', 'zid' => $v->id])) ?>
    </div>
    <?if ($otsenki[$v->id]['rabotnik_count'] == $otsenki[$v->id]['podpisannie_otsenki_count']):?>
        <div class="ball_row text-info">
            Средний балл: <?=$otsenki[$v->id]['avg_ball']?>
        </div>
    <?endif?>
    <?if ($v->status == \app\enums\StatusZayavleniyaNaAttestaciyu::PODPISANO_OTDELOM_ATTESTACII){?>
        <table class="tb fajly_tb">
            <tr>
                <td>Портфолио</td>
                <td>-</td>
                <td>
                    <div class="inline-block" id="portfolio<?=$v->id?>">
                        <?
                            if ($v->portfolio) echo $v->portfolioFajlRel->getFileLink('zayavlenie_fajl btn btn-link link-btn')
                        ?>
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
            <? if ($v->na_kategoriyu == \app\enums\KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA and count($v->otraslevoeSoglashenieZayavleniyaRel) == 0): ?>
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
        <?endif?>
        <? if ($v->na_kategoriyu == \app\enums\KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA): ?>
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


