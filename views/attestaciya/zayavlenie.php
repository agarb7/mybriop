<?php
use \app\helpers\Html;
use \app\entities\Fajl;

echo '<h3>'.$zayavlenie['familiya'].' '.$zayavlenie['imya'].' '.$zayavlenie['otchestvo'].'</h3>';

echo '<p><b>Должность </b>'.$zayavlenie['dolzhnost'].', '.$zayavlenie['rabota_organizaciya'].'</p>';

?>

<div class="panel panel-default">
    <div class="panel-heading"><b>Действующий аттестационный лист</b></div>
    <div class="panel-body">
        <div class="col-md-4 no-left-padding">
            <b>Категория</b><br>
            <?=\app\enums\KategoriyaPedRabotnika::namesMap()[$zayavlenie['attestaciya_kategoriya']]?>
        </div>
        <div class="col-md-4">
            <b>Период действия</b><br>
            с <?=date('d.m.Y',strtotime($zayavlenie['attestaciya_data_prisvoeniya']))?> по <?=date('d.m.y',strtotime($zayavlenie['attestaciya_data_okonchaniya_dejstviya']))?>
        </div>
        <div style="overflow:hidden" class="col-md-4 no-right-padding">
            <b>Копия действующего аттестационного листа</b><br>
            <?=Html::a($zayavlenie['kopiya_attestacionnogo_lista_vneshnee_imya_fajla'],
                Fajl::getFileUrl($zayavlenie['kopiya_attestacionnogo_lista_fajl_id']),
                ['target'=>'_blank'])?>
        </div>
    </div>
</div>

<?
    echo '<p><b>Категория, на которую будет производиться аттестация</b> '.\app\enums\KategoriyaPedRabotnika::namesMap()[$zayavlenie['na_kategoriyu']];
    if ($zayavlenie['var_ispytanie_2']){
        echo '<p><b>Второе вариативное испытание</b> '.mb_strtolower($zayavlenie['var_ispytanie_2']).'</p>';
    }
    if ($zayavlenie['var_ispytanie_3']){
        echo '<p><b>Второе вариативное испытание</b> '.mb_strtolower($zayavlenie['var_ispytanie_3']).'</p>';
    }
?>

<?
    if ($zayavlenie['svedeniya_o_sebe']){
?>
        <div class="panel panel-default">
            <div class="panel-heading"><b>Сведения о себе</b></div>
            <div class="panel-body">
                <b>Текст</b><br>
                <?=$zayavlenie['svedeniya_o_sebe']?>
                <br>
                <b>Файл</b><br>
                <?=Html::a($zayavlenie['svedeniya_s_sebe_vneshnee_imya_fajla'],
                    Fajl::getFileUrl($zayavlenie['svedeniya_o_sebe_fajl_id']),
                    ['dowload'=>$zayavlenie['svedeniya_s_sebe_vneshnee_imya_fajla'],'target'=>'_blank'])?>
            </div>
        </div>
<?
    }
?>

<p>
    <b>Время проведения аттестации </b>
    <?=
    'прием заявлений с '.
    \Yii::$app->formatter->asDate($zayavlenie['priem_zayavleniya_nachalo'],'php:d.m.Y').
    ' по '.\Yii::$app->formatter->asDate($zayavlenie['priem_zayavleniya_konec'],'php:d.m.Y').', '.
    'прохождения аттестации с '.
    \Yii::$app->formatter->asDate($zayavlenie['nachalo'],'php:d.m.Y').
    ' по '.\Yii::$app->formatter->asDate($zayavlenie['konec'],'php:d.m.Y');
    ?>
</p>

<div class="panel panel-default">
    <div class="panel-heading"><b>Стаж</b></div>
    <div class="panel-body">
        <div class="col-md-3 no-left-padding">
           <b>общий педагогический</b> <?=$zayavlenie['ped_stazh']?>
        </div>
        <div class="col-md-3">
            <b>в занимаемой должности</b> <?=$zayavlenie['stazh_v_dolzhnosti']?>
        </div>
        <div class="col-md-5 no-right-padding">
            <b>в данном обр. учр-ии по занимаемой должн.</b> <?=$zayavlenie['rabota_stazh_v_dolzhnosti']?>
        </div>
    </div>
</div>

<p><b>Копия трудовой книжки</b> <?=Html::a($zayavlenie['kopiya_trudovoj_vneshnee_imya_fajla'],
        Fajl::getFileUrl($zayavlenie['kopiya_trudovoj_fajl_id']),
        ['download'=>$zayavlenie['kopiya_trudovoj_vneshnee_imya_fajla'],'target'=>'_blank'])?></p>

<?

    if ($zayavlenie['obrazovaniya']){
?>
<p><b>Сведения о высшем образовании</b></p>
<table class="table table-bordered">
    <thead class="thead">
        <tr>
            <th>Организация</th>
            <th>Тип документа</th>
            <th>Квалификация</th>
            <th>Серия документа</th>
            <th>Номер документа</th>
            <th>Дата выдачи</th>
            <th>Копия документа</th>
        </tr>
    </thead>
    <tbody>
    <?
    foreach($zayavlenie['obrazovaniya'] as $k=>$v){
        echo Html::tag('td', $v['organizaciya']);
        echo Html::tag('td', \app\enums\TipDokumentaObObrazovanii::namesMap()[$v['dokument_ob_obrazovanii_tip']]);
        echo Html::tag('td', $v['obrazovanie_kvalifikaciya']);
        echo Html::tag('td', $v['dokument_ob_obrazovanii_seriya']);
        echo Html::tag('td', $v['dokument_ob_obrazovanii_nomer']);
        echo Html::tag('td', Yii::$app->formatter->asDate($v['dokument_ob_obrazovanii_data'],'php:d.m.Y'));
        echo Html::tag('td', Html::a($v['obrazovanie_vneshnee_imya_fajla'],
            Fajl::getFileUrl($v['obrazovanie_fajl_id']),
            ['download'=>$v['obrazovanie_vneshnee_imya_fajla'],'target'=>'_blank']));
    }
    ?>
    </tbody>
</table>


<?
}

if ($zayavlenie['kursy']){
?>
<p><b>Сведения о курсах повышения квалификации</b></p>
<table class="table table-bordered">
    <thead class="thead">
    <tr>
        <th>Организация</th>
        <th>Тип документа</th>
        <th>Название курса</th>
        <th>Количество часов</th>
        <th>Дата выдачи</th>
        <th>Копия документа</th>
    </tr>
    </thead>
    <tbody>
    <?
    foreach ($zayavlenie['kursy'] as $k=>$v) {
        echo Html::tag('td', $v['organizaciya']);
        echo Html::tag('td', \app\enums\TipDokumentaObObrazovanii::namesMap()[$v['dokument_ob_obrazovanii_tip']]);
        echo Html::tag('td', $v['kurs_nazvanie']);
        echo Html::tag('td', $v['kurs_chasy']);
        echo Html::tag('td', Yii::$app->formatter->asDate($v['dokument_ob_obrazovanii_data'],'php:d.m.Y'));
        echo Html::tag('td', Html::a($v['obrazovanie_vneshnee_imya_fajla'],
            Fajl::getFileUrl($v['obrazovanie_fajl_id']),
            ['download'=>$v['obrazovanie_vneshnee_imya_fajla'],'target'=>'_blank']));
    }
    ?>
    </tbody>
</table>

<?
}
?>
