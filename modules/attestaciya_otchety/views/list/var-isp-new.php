<?php
$this->title = 'Отчет по вариативным формам';

?>

<p>с <?= (\app\globals\ApiGlobals::dateToStr($vremya->nachalo))?> по <?=\app\globals\ApiGlobals::dateToStr($vremya->konec)?></p>

<table class="tb">
    <thead>
        <tr>
            <td></td>
            <? foreach ($ispytaniya as $item) : ?>
                <td colspan="4"><?= $item->nazvanie ?></td>
            <? endforeach ?>
        </tr>
        <tr>
            <td></td>
            <? foreach ($ispytaniya as $item) : ?>
                <td>Подтвержденные</td>
                <td>Не подтвержденные</td>
                <td>Отклоненные</td>
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
                        $podtverzhdeno = 0;
                        $ne_podtverzhdeno = 0;
                        $otkloneno = 0;
                        if (isset($item['var_isp'][$ispytanie['id']]))
                            $podtverzhdeno = $item['var_isp'][$ispytanie['id']]['podtverzhdeno'];
                        if (isset($item['var_isp'][$ispytanie['id']]))
                            $ne_podtverzhdeno = $item['var_isp'][$ispytanie['id']]['ne_podtverzhdeno'];
                        if (isset($item['var_isp'][$ispytanie['id']]))
                            $otkloneno = $item['var_isp'][$ispytanie['id']]['otkloneno'];
                    ?>
                    <td><?= $podtverzhdeno ?></td>
                    <td><?= $ne_podtverzhdeno ?></td>
                    <td><?= $otkloneno ?></td>
                    <td><?= $podtverzhdeno + $ne_podtverzhdeno + $otkloneno?></td>
                <? endforeach ?>
            </tr>
        <? endforeach; ?>
    </tbody>
</table>
