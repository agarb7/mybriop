<?php
$this->title = 'Отчет по вариативным формам';

?>

<p>с <?= (\app\globals\ApiGlobals::dateToStr($vremya->nachalo))?> по <?=\app\globals\ApiGlobals::dateToStr($vremya->konec)?></p>

<table class="tb">
    <thead>
    <tr>
        <td>Должность</td>
        <td class="center">Год</td>
        <td class="center">Месяц</td>
        <td class="center">Установлена высшая кв./к</td>
        <td class="center">Отказано в установлении высшей кв./к</td>
        <td class="center">Установлена первая кв./к</td>
        <td class="center">Отказано в установлении первой кв./к</td>
        <td class="center">Итого</td>
    </tr>
    </thead>
    <tbody>
        <? foreach ($report as $item) : ?>
        <tr>
            <td><?= $item['dolzhnost_nazvanie'] ?></td>
            <td class="center"><?= $item['year'] ?></td>
            <td class="center"><?= $item['month'] ?></td>
            <td class="center"><?= $item['vyshaya_reccomended'] ?></td>
            <td class="center"><?= $item['vyshaya_notreccomended'] ?></td>
            <td class="center"><?= $item['pervaya_reccomended'] ?></td>
            <td class="center"><?= $item['pervaya_notreccomended'] ?></td>
            <td class="center"><?= $item['all_zayavleniya'] ?></td>
        </tr>
        <? endforeach ?>
    </tbody>
</table>
