<?php
use \app\globals\ApiGlobals;
/**
 * @var \app\entities\ZayavlenieNaAttestaciyu $zayavlenie
 */
?>

<h4><?=$zayavlenie->fizLicoRel->getFio()?></h4>

<?php
    if ($zayavlenie->ld_olimpiady){
        echo ApiGlobals::parse_text($zayavlenie->getAttributeLabel('ld_olimpiady').':','paragraph bold');
        echo ApiGlobals::parse_text($zayavlenie->ld_olimpiady,'paragraph');
    }
    if ($zayavlenie->ld_posobiya){
        echo ApiGlobals::parse_text($zayavlenie->getAttributeLabel('ld_posobiya').':','paragraph bold');
        echo ApiGlobals::parse_text($zayavlenie->ld_posobiya,'paragraph');
    }
    if ($zayavlenie->ld_publikacii){
        echo ApiGlobals::parse_text($zayavlenie->getAttributeLabel('ld_publikacii').':','paragraph bold');
        echo ApiGlobals::parse_text($zayavlenie->ld_publikacii,'paragraph');
    }
    if ($zayavlenie->ld_prof_konkursy){
        echo ApiGlobals::parse_text($zayavlenie->getAttributeLabel('ld_prof_konkursy').':','paragraph bold');
        echo ApiGlobals::parse_text($zayavlenie->ld_prof_konkursy,'paragraph');
    }
    if ($zayavlenie->ld_obshestvennaya_aktivnost){
        echo ApiGlobals::parse_text($zayavlenie->getAttributeLabel('ld_obshestvennaya_aktivnost').':','paragraph bold');
        echo ApiGlobals::parse_text($zayavlenie->ld_obshestvennaya_aktivnost,'paragraph');
    }
    if ($zayavlenie->ld_elektronnye_resursy){
        echo ApiGlobals::parse_text($zayavlenie->getAttributeLabel('ld_elektronnye_resursy').':','paragraph bold');
        echo ApiGlobals::parse_text($zayavlenie->ld_elektronnye_resursy,'paragraph');
    }
    if ($zayavlenie->ld_otkrytoe_meropriyatie){
        echo ApiGlobals::parse_text($zayavlenie->getAttributeLabel('ld_otkrytoe_meropriyatie').':','paragraph bold');
        echo ApiGlobals::parse_text($zayavlenie->ld_otkrytoe_meropriyatie,'paragraph');
    }
    if ($zayavlenie->ld_nastavnik){
        echo ApiGlobals::parse_text($zayavlenie->getAttributeLabel('ld_nastavnik').':','paragraph bold');
        echo ApiGlobals::parse_text($zayavlenie->ld_nastavnik,'paragraph');
    }
    if ($zayavlenie->ld_deti_sns){
        echo ApiGlobals::parse_text($zayavlenie->getAttributeLabel('ld_deti_sns').':','paragraph bold');
        echo ApiGlobals::parse_text($zayavlenie->ld_deti_sns,'paragraph');
    }
?>
