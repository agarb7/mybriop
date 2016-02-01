<?php
use app\globals\KursGlobals;

/* @var $id integer Kurs id */

$kug = KursGlobals::get_kug($id);
$attestaciya = KursGlobals::get_attestatciya($id);
$max_week_num = KursGlobals::get_max_week_of_kurs($id);
?>

<h2>Учебный план</h2>

<?= KursGlobals::get_uchebnii_plan_html($kug,$attestaciya) ?>

<h2>Календарный учебный график</h2>

<?= KursGlobals::get_kug_html($kug,$attestaciya,$max_week_num) ?>
