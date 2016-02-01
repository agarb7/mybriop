<?php

    use \app\components\Months;
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
    В настоящее время:
</p>
<p class="paragraph">
    С формами проведения аттестации педагогических работников для установления квалификационных категорий
    (утв. приказом МОиН РБ от 10.04.2015 г. № 828) ознакомлен(а).
</p>
<p class="paragraph">
    Считаю наиболее приемлемым прохождение третьего этапа аттестации на высшую
    квалификационную категорию в форме "<?=$zayavlenie->attestacionnoeVariativnoeIspytanie3Rel->nazvanie?>"
</p>

<p class="paragraph">
    Подтверждаю свое согласие на обработку отделом аттестации и развития
    профессиональных квалификаций АОУ ДПО РБ «БРИОП» моих персональных данных
    (Приложение к заявлению).
</p>
<?php
//??????????????????
//Аттестацию на заседании аттестационной комиссии прошу провести
?>
<p class="paragraph">
    С порядком проведения аттестации педагогических работников организаций,
    осуществляющих образовательную деятельность ознакомлен(а).
</p>
<p class="paragraph">
  Телефон: <?=$zayavlenie->fizLicoRel->telefon?>
</p>
<p class="paragraph">
  e-mail: <?=$zayavlenie->fizLicoRel->email?>
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