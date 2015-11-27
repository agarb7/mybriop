<?php
use app\entities\Kim;
use app\helpers\Val;

/* @var $kimRecord Kim*/

?>
<div class="kursslushatelyu-kimtekst">
    <?= Val::format('paragraph', $kimRecord, 'text') ?>
</div>
