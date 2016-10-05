<?php
use \app\helpers\Html;
use \app\entities\Fajl;

/**
 * @var \app\entities\ZayavlenieNaAttestaciyu $zayavlenie
 */

echo '<h3>'.$zayavlenie->fizLicoRel->getFio().'</h3>';

echo '<h4> E-mail: '.$zayavlenie->fizLicoRel->email.'</h4>';

echo '<p><b>Должность </b>'.$zayavlenie->dolzhnostRel->nazvanie.', '.$zayavlenie->organizaciyaRel->nazvanie.'</p>';

?>

<div class="panel panel-default">
    <div class="panel-heading"><b>Действующий аттестационный лист</b></div>
    <div class="panel-body">
        <div class="col-md-4 no-left-padding">
            <b>Категория</b><br>
            <?=\app\enums\KategoriyaPedRabotnika::namesMap()[$zayavlenie->attestaciya_kategoriya]?>
        </div>
        <div class="col-md-4">
            <? if ($zayavlenie->attestaciya_kategoriya != \app\enums\KategoriyaPedRabotnika::BEZ_KATEGORII):?>
                <b>Период действия</b><br>
                с <?=date('d.m.Y',strtotime($zayavlenie->attestaciya_data_prisvoeniya))?> по <?=date('d.m.y',strtotime($zayavlenie->attestaciya_data_okonchaniya_dejstviya))?>
            <?endif?>
        </div>
        <div style="overflow:hidden" class="col-md-4 no-right-padding">
            <? if ($zayavlenie->attestaciya_kategoriya != \app\enums\KategoriyaPedRabotnika::BEZ_KATEGORII):?>
                <b>Копия действующего аттестационного листа</b><br>
                <?=Html::a($zayavlenie->attestaciyaFajlRel->vneshnee_imya_fajla,
                    $zayavlenie->attestaciyaFajlRel->getUri(),
                    ['target'=>'_blank'])?>
            <?endif?>
        </div>
    </div>
</div>

<?
    echo '<p><b>Категория, на которую будет производиться аттестация</b> '.\app\enums\KategoriyaPedRabotnika::namesMap()[$zayavlenie->na_kategoriyu];
    if ($zayavlenie->var_ispytanie_2){
        echo '<p><b>Второе вариативное испытание</b> '.mb_strtolower($zayavlenie->attestacionnoeVariativnoeIspytanie2Rel->nazvanie).'</p>';
    }
    if ($zayavlenie->var_ispytanie_3){
        echo '<p><b>Третье вариативное испытание</b> '.mb_strtolower($zayavlenie->attestacionnoeVariativnoeIspytanie3Rel->nazvanie).'</p>';
    }
?>

<?
    if ($zayavlenie->svedeniya_o_sebe){
?>
        <div class="panel panel-default">
            <div class="panel-heading"><b>Сведения о себе</b></div>
            <div class="panel-body">
                <b>Текст</b><br>
                <?=$zayavlenie->svedeniya_o_sebe?>
                <br>
                <b>Файл</b><br>
                <?=Html::a($zayavlenie->svedeniyaOSebeFajlRel->vneshnee_imya_fajla,
                    $zayavlenie->svedeniyaOSebeFajlRel->getUri(),
                    ['dowload'=>$zayavlenie->svedeniyaOSebeFajlRel->vneshnee_imya_fajla,'target'=>'_blank'])?>
            </div>
        </div>
<?
    }
?>

<p>
    <b>Время проведения аттестации </b>
    <?=
    'прием заявлений с '.
    \Yii::$app->formatter->asDate($zayavlenie->vremyaProvedeniyaAttestaciiRel->priem_zayavleniya_nachalo,'php:d.m.Y').
    ' по '.\Yii::$app->formatter->asDate($zayavlenie->vremyaProvedeniyaAttestaciiRel->priem_zayavleniya_konec,'php:d.m.Y').', '.
    'прохождения аттестации с '.
    \Yii::$app->formatter->asDate($zayavlenie->vremyaProvedeniyaAttestaciiRel->nachalo,'php:d.m.Y').
    ' по '.\Yii::$app->formatter->asDate($zayavlenie->vremyaProvedeniyaAttestaciiRel->konec,'php:d.m.Y');
    ?>
</p>

<div class="panel panel-default">
    <div class="panel-heading"><b>Стаж</b></div>
    <div class="panel-body">
        <div class="col-md-3 no-left-padding">
           <b>общий педагогический</b> <?=$zayavlenie->ped_stazh?>
        </div>
        <div class="col-md-3">
            <b>в занимаемой должности</b> <?=$zayavlenie->stazh_v_dolzhnosti?>
        </div>
        <div class="col-md-5 no-right-padding">
            <b>в данном обр. учр-ии по занимаемой должн.</b> <?=$zayavlenie->rabota_stazh_v_dolzhnosti?>
        </div>
    </div>
</div>

<p><b>Копия трудовой книжки</b> <?=Html::a($zayavlenie->kopiyaTruidovoiajlRel->vneshnee_imya_fajla,
        $zayavlenie->kopiyaTruidovoiajlRel->getUri(),
        ['download'=>$zayavlenie->kopiyaTruidovoiajlRel->vneshnee_imya_fajla,'target'=>'_blank'])?></p>

<div class="panel panel-default">
    <div class="panel-heading"><b>Дата назначения на должность</b></div>
    <div class="panel-body">
        <div class="col-md-4 no-left-padding">
            <b>впервые</b> <?=\Yii::$app->formatter->asDate($zayavlenie->rabota_data_naznacheniya,'php:d.m.Y')?>
        </div>
        <div class="col-md-4">
            <b>в данном учреждении</b> <?=\Yii::$app->formatter->asDate($zayavlenie->rabota_data_naznacheniya_v_uchrezhdenii,'php:d.m.Y')?>
        </div>
    </div>
</div>

<?php
    if ($zayavlenie->otraslevoeSoglashenieZayavleniyaRel){
?>
<h4>Отраслевое соглашение</h4>
<table class="table table-bordered">
    <thead class="thead">
    <tr>
        <th>Тип</th>
        <th>Название</th>
        <th>Подтверждающий документ</th>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach ($zayavlenie->otraslevoeSoglashenieZayavleniyaRel as $os) {
            /**
             * @var \app\entities\OtraslevoeSoglashenieZayavleniya $os
             */
            echo '<tr>';
            echo Html::tag('td', \app\enums\TipOtraslevogoSoglashenijya::namesMap()[$os->otraslevoeSoglashenieRel->tip]);
            echo Html::tag('td', $os->otraslevoeSoglashenieRel->nazvanie);
            echo Html::tag('td', (isset($os->fajlRel) ? Html::a($os->fajlRel->vneshnee_imya_fajla : ''),
                (isset($os->fajlRel) ? $os->fajlRel->getUri() : ''),
                ['target'=>'_blank']
            ));
            echo '</tr>';
        }
    ?>
    </tbody>
</table>
<?
    }
?>

<?

    if ($zayavlenie->obrazovaniyaRel){
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
    foreach($zayavlenie->obrazovaniyaRel as $obrazovanie){
        /**
         * @var \app\entities\ObrazovanieDlyaZayavleniyaNaAttestaciyu $obrazovanie
         */
        echo '<tr>';
        echo Html::tag('td', $obrazovanie->obrazovanieOrganizaciyaRel->nazvanie);
        echo Html::tag('td', \app\enums\TipDokumentaObObrazovanii::namesMap()[$obrazovanie->dokument_ob_obrazovanii_tip]);
        echo Html::tag('td', $obrazovanie->obrazovanieKvalifikaciyaRel->nazvanie);
        echo Html::tag('td', $obrazovanie->dokument_ob_obrazovanii_seriya);
        echo Html::tag('td', $obrazovanie->dokument_ob_obrazovanii_nomer);
        echo Html::tag('td', Yii::$app->formatter->asDate($obrazovanie->dokument_ob_obrazovanii_data,'php:d.m.Y'));
        echo Html::tag('td', Html::a($obrazovanie->obrazovanieFajlRel->vneshnee_imya_fajla,
            $obrazovanie->obrazovanieFajlRel->getUri(),
            ['download'=>$obrazovanie->obrazovanieFajlRel->vneshnee_imya_fajla,'target'=>'_blank']));
        echo '<tr>';
    }
    ?>
    </tbody>
</table>


<?
}

if ($zayavlenie->kursyRel){
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
    foreach ($zayavlenie->kursyRel as $kurs) {
        /**
         * @var \app\entities\ObrazovanieDlyaZayavleniyaNaAttestaciyu $kurs
         */
        echo '<tr>';
        echo Html::tag('td', $kurs->kursOrganizaciyaRel->nazvanie);
        echo Html::tag('td', \app\enums\TipDokumentaObObrazovanii::namesMap()[$kurs->dokument_ob_obrazovanii_tip]);
        echo Html::tag('td', $kurs->kurs_nazvanie);
        echo Html::tag('td', $kurs->kurs_chasy);
        echo Html::tag('td', Yii::$app->formatter->asDate($kurs->dokument_ob_obrazovanii_data,'php:d.m.Y'));
        echo Html::tag('td', Html::a($kurs->kursFajlRel->vneshnee_imya_fajla,
            $kurs->kursFajlRel->getUri(),
            ['download'=>$kurs->kursFajlRel->vneshnee_imya_fajla,'target'=>'_blank']));
        echo '</tr>';
    }
    ?>
    </tbody>
</table>

<?
}
?>

<p><b>Домашний телефон: </b>8<?=$zayavlenie->domashnij_telefon?></p>

<?php
    if ($zayavlenie->provesti_zasedanie_bez_prisutstviya){
        echo Html::tag('b','Провести заседании аттестационной комиссии без моего присутствия').': ✓';
    }
    if ($zayavlenie->na_kategoriyu == \app\enums\KategoriyaPedRabotnika::PERVAYA_KATEGORIYA){
        echo Html::tag('p',$zayavlenie->getAttributeLabel('prilozhenie1'),['class' => 'bold']);
        echo Html::tag('p',$zayavlenie->prilozhenie1);
    }
    else{
        echo '<h4>Личные достижения</h4>';
        if ($zayavlenie->ld_olimpiady){
            echo Html::tag('p',$zayavlenie->getAttributeLabel('ld_olimpiady'),['class' => 'bold']);
            echo Html::tag('p',$zayavlenie->ld_olimpiady);
        }
        if ($zayavlenie->ld_posobiya){
            echo Html::tag('p',$zayavlenie->getAttributeLabel('ld_posobiya'),['class' => 'bold']);
            echo Html::tag('p',$zayavlenie->ld_posobiya);
        }
        if ($zayavlenie->ld_publikacii){
            echo Html::tag('p',$zayavlenie->getAttributeLabel('ld_publikacii'),['class' => 'bold']);
            echo Html::tag('p',$zayavlenie->ld_publikacii);
        }
        if ($zayavlenie->ld_prof_konkursy){
            echo Html::tag('p',$zayavlenie->getAttributeLabel('ld_prof_konkursy'),['class' => 'bold']);
            echo Html::tag('p',$zayavlenie->ld_prof_konkursy);
        }
        if ($zayavlenie->ld_obshestvennaya_aktivnost){
            echo Html::tag('p',$zayavlenie->getAttributeLabel('ld_obshestvennaya_aktivnost'),['class' => 'bold']);
            echo Html::tag('p',$zayavlenie->ld_obshestvennaya_aktivnost);
        }
        if ($zayavlenie->ld_elektronnye_resursy){
            echo Html::tag('p',$zayavlenie->getAttributeLabel('ld_elektronnye_resursy'),['class' => 'bold']);
            echo Html::tag('p',$zayavlenie->ld_elektronnye_resursy);
        }
        if ($zayavlenie->ld_otkrytoe_meropriyatie){
            echo Html::tag('p',$zayavlenie->getAttributeLabel('ld_otkrytoe_meropriyatie'),['class' => 'bold']);
            echo Html::tag('p',$zayavlenie->ld_otkrytoe_meropriyatie);
        }
        if ($zayavlenie->ld_nastavnik){
            echo Html::tag('p',$zayavlenie->getAttributeLabel('ld_nastavnik'),['class' => 'bold']);
            echo Html::tag('p',$zayavlenie->ld_nastavnik);
        }
        if ($zayavlenie->ld_deti_sns){
            echo Html::tag('p',$zayavlenie->getAttributeLabel('ld_deti_sns'),['class' => 'bold']);
            echo Html::tag('p',$zayavlenie->ld_deti_sns);
        }
    }
?>


