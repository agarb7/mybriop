<?php
use app\globals\KursGlobals;

/* @var $id integer Kurs id */

$kug = KursGlobals::get_kug($id);
$attestaciya = KursGlobals::get_attestatciya($id);
$max_week_num = KursGlobals::get_max_week_of_kurs($id);
?>

<h3>Учебный план</h3>

<?= KursGlobals::get_uchebnii_plan_html($kug,$attestaciya) ?>

<h3>Календарный учебный график</h3>

<?= KursGlobals::get_kug_html($kug,$attestaciya,$max_week_num) ?>
