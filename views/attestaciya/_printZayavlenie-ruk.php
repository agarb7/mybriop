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
    на соотвествие требованиям, установленным квалификационной характеристикой,
    должности <?=$zayavlenie->dolzhnostRel->nazvanie?>.
</p>

<p class="paragraph">
    С порядком проведения аттестации кандидатов на должность руководителя и руководителя образовательной организации, находящейся в ведении МОиН РБ (утв. приказом от 30.06.2015 г. № 1593) ознакомлен(а).
</p>

<!---->
<!--<p class="paragraph">Сведения о повышении квалификации за последние 5 лет:</p>-->
<?php
//    $content = '';
//
//    foreach ($zayavlenie->kursyRel as $item) {
//        /**
//         * @var \app\entities\ObrazovanieDlyaZayavleniyaNaAttestaciyu $item
//         */
//        $content .= '<li>'.
//            $item->kursOrganizaciyaRel->nazvanie.', '.
//            ApiGlobals::dateToStr($item->dokument_ob_obrazovanii_data,'Y').' г., '.
//            $item->kurs_chasy.' ч.'.
//            '</li>';
//    }
//
//    echo '<ul class="double-indent">'.$content.'</ul>';
//?>


<p class="paragraph">
    Подтверждаю свое согласие на обработку отделом аттестации и развития
    профессиональных квалификаций ГАУ ДПО «БРИОП» моих персональных данных
    (Приложение к заявлению).
</p>
<p class="paragraph">
    Подтверждаю свое согласие на обработку отделом аттестации и развития
    профессиональных квалификаций ГАУ ДПО РБ «БРИОП» моих персональных данных _______________ (подпись заявителя)
</p>
<p class="paragraph">
    Телефон дом.: <?=$zayavlenie->domashnij_telefon
        ? '8'.$zayavlenie->domashnij_telefon
        : 'Данные не указаны'
    ?>
    &nbsp;
    Телефон сот.: <?=$zayavlenie->fizLicoRel->telefon
        ? '8'.$zayavlenie->fizLicoRel->telefon
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
    Сведения о руководителе образовательной организации (кандидате на должность руководителя):
</p>

<p class="paragraph">Дата рождения: <?=$zayavlenie->data_rozhdeniya
        ? date('d.m.Y',strtotime($zayavlenie->data_rozhdeniya))
        : 'Данные не предоставлены'?></p>

<p class="paragraph">Дата назначения на должность: <?=ApiGlobals::dateToStr($zayavlenie->rabota_data_naznacheniya)?></p>

<p class="paragraph">Стаж руководящей работы: <?=$zayavlenie->stazh_rukovodyashej_raboty?></p>

<p class="paragraph">Дата назначения на должность в данной образовательной организации: <?=ApiGlobals::dateToStr($zayavlenie->rabota_data_naznacheniya_v_uchrezhdenii)?></p>

<p class="paragraph">Стаж работы в данном учреждении: <?= $zayavlenie->rabota_stazh_v_dolzhnosti ?></p>

<p class="paragraph">Стаж педагогической работы: <?= $zayavlenie->ped_stazh ?></p>

<p class="paragraph">Общий трудовой стаж: <?= $zayavlenie->stazh_obshij_trudovoj ?></p>

<p class="paragraph">Сведения о профессиональном образовании, профессиональной переподготовке, наличии ученой степени, ученого звания:</p>
    <?
    $content = '';
    foreach ($zayavlenie->obrazovaniyaRel as $item) {
        /**
         * @var \app\entities\ObrazovanieDlyaZayavleniyaNaAttestaciyu $item
         */
        $content .= '<li>' .
            $item->obrazovanieOrganizaciyaRel->nazvanie . ', ' .
            ApiGlobals::dateToStr($item->dokument_ob_obrazovanii_data, 'Y') . ' г., ' .
            $item->obrazovanieKvalifikaciyaRel->nazvanie .
            '</li>';
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