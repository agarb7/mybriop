<?php

    use \app\components\Months;
    use \app\enums\KategoriyaPedRabotnika;
    use \app\globals\ApiGlobals;
    /** @var app\entities\ZayavlenieNaAttestaciyu $zayavlenie */
?>
<div style="text-align:right">
В аттестационную комиссию  МОиН РБ<br>

от <?=$zayavlenie->familiya.' '.$zayavlenie->imya.' '.$zayavlenie->otchestvo?><br>

<?=$zayavlenie->dolzhnostRel->nazvanie?><br>

<?=$zayavlenie->organizaciyaRel->nazvanie?>
</div>
<div style="height: 2em">&nbsp;</div>
<p style="text-align: center;">ЗАЯВЛЕНИЕ</p>
<p class="paragraph">
    <?php
        $nachalo_attestacii_timestamp = strtotime($zayavlenie->vremyaProvedeniyaAttestaciiRel->nachalo)
    ?>

    Прошу аттестовать меня в
    <?=Months::getMonthName(date('n',$nachalo_attestacii_timestamp),Months::PAD_PREDLOGNIJ)?> <?=date('Y',$nachalo_attestacii_timestamp)?> года
    на
    <?=
        $zayavlenie->na_kategoriyu == \app\enums\KategoriyaPedRabotnika::PERVAYA_KATEGORIYA
            ? 'первую квалификационную категорию'
            : 'высшую квалификационную категорию'
    ?>
    по должности <?=$zayavlenie->dolzhnostRel->nazvanie?>.
</p>
<p class="paragraph">
    В настоящее время: <?= $zayavlenie->attestaciya_kategoriya == KategoriyaPedRabotnika::BEZ_KATEGORII
        ? 'не имею категорию'
        : ($zayavlenie->attestaciya_kategoriya == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA
            ? 'имею первую квалификационную категорию'
            : 'имею высшую квалификационную категорию').
            ', срок ее действия до '.date('d.m.Y',strtotime($zayavlenie->attestaciyaDataOkonchaniyaDejstviya))
    ?>
</p>
<p class="paragraph">
    Сообщаю о себе следующие сведения:
</p>
<p class="paragraph">
    Дата рождения: <?=$zayavlenie->data_rozhdeniya
        ? date('d.m.Y',strtotime($zayavlenie->data_rozhdeniya))
        : 'Данные не предоставлены'?>
</p>

<p class="paragraph">
    Район: <?=$zayavlenie->organizaciyaRel->adres_adresnyj_objekt ?
                $zayavlenie->organizaciyaRel->adresAdresnyjObjektRel->oficialnoe_nazvanie
                : 'Данные не предоставлены' ?>
</p>

<div class="">
    <table class="tb indent-block">
        <tr>
            <td rowspan="2" style="vertical-align: top; border:0">Стаж:</td>
            <td class="center">педагогической работы</td>
            <td class="center">в данном учреждении</td>
            <td class="center">в данной должности</td>
        </tr>
        <tr>
            <td class="center"><?=$zayavlenie->ped_stazh?></td>
            <td class="center"><?=$zayavlenie->rabota_stazh_v_dolzhnosti?></td>
            <td class="center"><?=$zayavlenie->stazh_v_dolzhnosti?></td>
        </tr>
    </table>
</div>
<p></p>
<div class="">
    <table class="tb indent-block">
        <tr>
            <td rowspan="2" style="vertical-align: top; border:0">Дата назначения на должность:</td>
            <td class="center">на данную должность впервые</td>
            <td class="center">в данном учреждении</td>
        </tr>
        <tr>
            <td class="center"><?=ApiGlobals::dateToStr($zayavlenie->rabota_data_naznacheniya)?></td>
            <td class="center"><?=ApiGlobals::dateToStr($zayavlenie->rabota_data_naznacheniya_v_uchrezhdenii)?></td>
        </tr>
    </table>
</div>
<p></p>

<p class="paragraph">Сведения о профессиональном образовании, почетных званиях:</p>
<?php
    $content = '';
    foreach ($zayavlenie->obrazovaniyaRel as $item) {
        /**
         * @var \app\entities\ObrazovanieDlyaZayavleniyaNaAttestaciyu $item
         */
        $content .= '<li>'.
                        $item->obrazovanieOrganizaciyaRel->nazvanie.', '.
                        ApiGlobals::dateToStr($item->dokument_ob_obrazovanii_data,'Y').' г., '.
                        $item->obrazovanieKvalifikaciyaRel->nazvanie.
                    '</li>';
    }

    foreach ($zayavlenie->otraslevoeSoglashenieZayavleniyaRel as $item) {
        /**
         * @var \app\entities\OtraslevoeSoglashenieZayavleniya $item
         */
        $content .= '<li>'.$item->otraslevoeSoglashenieRel->nazvanie.'</li>';
    }

    echo '<ul class="double-indent">'.$content.'</ul>';
?>

<p class="paragraph">Сведения о повышении квалификации за последние 5 лет:</p>
<?php
    $content = '';

    foreach ($zayavlenie->kursyRel as $item) {
        /**
         * @var \app\entities\ObrazovanieDlyaZayavleniyaNaAttestaciyu $item
         */
        $content .= '<li>'.
            $item->kursOrganizaciyaRel->nazvanie.', '.
            ApiGlobals::dateToStr($item->dokument_ob_obrazovanii_data,'Y').' г., '.
            $item->kurs_chasy.' ч.'.
            '</li>';
    }

    echo '<ul class="double-indent">'.$content.'</ul>';
?>

<p class="paragraph">
    С формами проведения аттестации педагогических работников для установления квалификационных категорий
    (утв. приказом МОиН РБ от 10.04.2015 г. № 828) ознакомлен(а).
</p>
<? if ($zayavlenie->na_kategoriyu == KategoriyaPedRabotnika::VYSSHAYA_KATEGORIYA and isset($zayavlenie->attestacionnoeVariativnoeIspytanie3Rel)) :?>
<p class="paragraph">
    Считаю наиболее приемлемым прохождение третьего этапа аттестации на высшую
    квалификационную категорию в форме "<?=$zayavlenie->attestacionnoeVariativnoeIspytanie3Rel->nazvanie?>"
</p>
<?endif;?>

<p class="paragraph">
    Подтверждаю свое согласие на обработку отделом аттестации и развития
    профессиональных квалификаций ГАУ ДПО «БРИОП» моих персональных данных
    (Приложение к заявлению).
</p>
<p class="paragraph">
    С порядком проведения аттестации педагогических работников организаций,
    осуществляющих образовательную деятельность ознакомлен(а).
</p>
<p class="paragraph">
    Личный телефон: <?=$zayavlenie->domashnij_telefon
        ? '8'.$zayavlenie->domashnij_telefon
        : 'Данные не указаны'
    ?>
    &nbsp;
    Рабочий телефон: <?=$zayavlenie->rabochij_telefon
        ? '8'.$zayavlenie->rabochij_telefon
        : 'Данные не указаны'
    ?>
</p>
<p class="paragraph">
  e-mail: <?=$zayavlenie->fizLicoRel->email ? $zayavlenie->fizLicoRel->email : 'Данные не указаны'?>
</p>
<table style="width: 100%">
    <tr>
        <td style="text-align: left;" class="indent">
            "<?=date('d')?>" <?=Months::getMonthName(date('n'),Months::PAD_RODITELNIJ)?> <?=date('Y')?> г.
        </td>
        <td style="text-align: right;">
            Подпись ____________________
        </td>
    </tr>
</table>

<pagebreak />

<p style="text-align: right;font-weight: bold">
    Приложение 1
</p>
<p class="paragraph">
Основанием для аттестации на указанную в заявлении квалификационную
категорию считаю следующие результаты работы,
соответствующие требованиям, предъявляемым к
<?=$zayavlenie->na_kategoriyu == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA
    ? 'первой'
    : 'высшей'
?> квалификационной категории
</p>

<p class="paragraph">
    <?
        if ($zayavlenie->na_kategoriyu == KategoriyaPedRabotnika::PERVAYA_KATEGORIYA) {
            echo ApiGlobals::parse_text($zayavlenie->prilozhenie1, 'paragraph');
        }
        else{
            echo '<p class="paragraph">Результаты участия обучающихся в предметных олимпиадах, конкурсах:</p>';
            echo ApiGlobals::parse_text($zayavlenie->ld_olimpiady, 'paragraph');
            echo '<p class="paragraph">Наличие опубликованных собственных методических разработок, методических материалов (программ, учебных и учебно-методических пособий, диагностических материалов, цифровых образовательных ресурсов), прошедших независимую экспертизу, имеющих соответствующий гриф и выходные данные:</p>';
            echo ApiGlobals::parse_text($zayavlenie->ld_posobiya, 'paragraph');
            echo '<p class="paragraph">Наличие опубликованных статей, научных публикаций, имеющих соответствующий гриф и выходные данные:</p>';
            echo ApiGlobals::parse_text($zayavlenie->ld_publikacii, 'paragraph');
            echo '<p class="paragraph">Результативность участия в профессиональных конкурсах:</p>';
            echo ApiGlobals::parse_text($zayavlenie->ld_prof_konkursy, 'paragraph');
            echo '<p class="paragraph">Общественная активность педагога: участие в экспертных комиссиях, предметных комиссиях (ЕГЭ, ГИА), в жюри конкурсов, творческих группах:</p>';
            echo ApiGlobals::parse_text($zayavlenie->ld_obshestvennaya_aktivnost, 'paragraph');
            echo '<p class="paragraph">Использование электронных образовательных ресурсов (ЭОР) в образовательном процессе:</p>';
            echo ApiGlobals::parse_text($zayavlenie->ld_elektronnye_resursy, 'paragraph');
            echo '<p class="paragraph">Публичное представление собственного педагогического опыта в форме открытого мероприятия:</p>';
            echo ApiGlobals::parse_text($zayavlenie->ld_otkrytoe_meropriyatie, 'paragraph');
            echo '<p class="paragraph">Исполнение функций наставника:</p>';
            echo ApiGlobals::parse_text($zayavlenie->ld_nastavnik, 'paragraph');
            echo '<p class="paragraph">Работа с детьми из СНС (социально неблагополучных семей):</p>';
            echo ApiGlobals::parse_text($zayavlenie->ld_deti_sns, 'paragraph');
        }
    ?>
</p>

<table style="width: 100%">
    <tr>
        <td style="text-align: left;" class="indent">
            "<?=date('d')?>" <?=Months::getMonthName(date('n'),Months::PAD_RODITELNIJ)?> <?=date('Y')?> г.
        </td>
        <td style="text-align: right;">
            Подпись ____________________
        </td>
    </tr>
</table>