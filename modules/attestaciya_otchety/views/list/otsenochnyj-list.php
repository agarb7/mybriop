<h1><?=$zayavlenie->getFio()?></h1>
<h2>Оценочные листы по испытанию "<?=$ispytanie->nazvanie?>"</h2>
<? $currentRabotnik = -1; ?>
<? foreach ($data as $otsenochnyjList): ?>
    <? $total = 0; ?>
    <? if ($currentRabotnik != $otsenochnyjList->rabotnikKomissiiFizLicoRel->id): ?>
        <? $currentRabotnik = $otsenochnyjList->rabotnikKomissiiFizLicoRel->id ?>
        <h3>Сотрудник комисси - <?= $otsenochnyjList->rabotnikKomissiiFizLicoRel->getFio() ?></h3>
    <? endif; ?>
    <h4>Оценочный лист "<?=$otsenochnyjList->nazvanie?>"</h4>
    <table class="tb">
        <thead>
            <tr>
                <td class="center">№</td>
                <td>Название</td>
                <td class="center">Оценка</td>
            </tr>
        </thead>
        <tbody>
            <? foreach($otsenochnyjList->strukturaOtsenochnogoListaZayvaleniyaRel as $structura):?>
                <? if ($structura->uroven == 1) $total += $structura->bally ?>
                <tr>
                    <td class="center"><?=$structura->nomer?></td>
                    <td><?=$structura->nazvanie?></td>
                    <td class="center"><?=$structura->bally?></td>
                </tr>
            <?endforeach?>
            <tr>
                <td colspan="2" class="right">Итого</td>
                <td class="center"><?=$total?></td>
            </tr>
        </tbody>
    </table>
<?endforeach?>
