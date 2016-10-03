<?php
use app\entities\KursExtended;
use app\helpers\Val;
use yii\web\View;

/**
 * @var $kursRecord KursExtended
 * @var $this View
 */

$this->title = Val::asText($kursRecord, 'nazvanie');
?>
<div class="kursslushatelyu-programmakursa">
    <p>Программа для курса
        <b>"<?= Val::asText($kursRecord, 'nazvanie') ?>"</b>
        пока ещё не загружена в систему.</p>
</div>