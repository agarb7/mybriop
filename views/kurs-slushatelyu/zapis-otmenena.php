<?php

use app\entities\Kurs;
use app\helpers\Html;
use app\models\kurs_slushatelyu\ZapisNaKursForm;
use app\widgets\KursSummary;

/**
 * @var ZapisNaKursForm $model
 */

$kurs = Kurs::findOne($model->kurs);

?>

<div class="jumbotron">
    <h2>Вы отменили запись на курс «<?= $kurs->nazvanie ?>» </h2>
    <?= KursSummary::widget([
        'model' => $kurs
    ]) ?>
    <?= Html::returningA('Ок', ['class' => 'btn btn-primary btn-lg center-block']) ?>
</div>
