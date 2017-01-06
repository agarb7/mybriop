<?php
$this->title = 'Отчет по вариативным формам';

?>

<p>с <?= (\app\globals\ApiGlobals::dateToStr($vremya->nachalo))?> по <?=\app\globals\ApiGlobals::dateToStr($vremya->konec)?></p>

<table class="tb">
    <thead>
        <tr>
            <td></td>
            <? foreach ($ispytaniya as $item) : ?>
                <td colspan="3"><?= $item->nazvanie ?></td>
            <? endforeach ?>
        </tr>
        <tr>
            <td></td>
            <? foreach ($ispytaniya as $item) : ?>
                <td>Рекомендованные</td>
                <td>Не рекомендованные</td>
                <td>Всего</td>
            <? endforeach ?>
        </tr>
    </thead>
    <tbody>
        <? foreach ($report as $item): ?>
            <tr>
                <td><?= $item['dolzhnost_nazvanie'] ?></td>
                <? foreach ($ispytaniya as $ispytanie) : ?>
                    <?
                        $reccomended = 0;
                        $notreccomended = 0;
                        if (isset($item['var_isp'][$ispytanie['id']]))
                            $reccomended = $item['var_isp'][$ispytanie['id']]['reccomended'];
                        if (isset($item['var_isp'][$ispytanie['id']]))
                            $notreccomended = $item['var_isp'][$ispytanie['id']]['notreccomended'];
                    ?>
                    <td><?= $reccomended ?></td>
                    <td><?= $notreccomended ?></td>
                    <td><?= $reccomended + $notreccomended?></td>
                <? endforeach ?>
            </tr>
        <? endforeach; ?>
    </tbody>
</table>
