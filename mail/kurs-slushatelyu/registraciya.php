<?php

use app\entities\FizLico;
use app\entities\Kurs;
use app\enums\TipFinansirovaniya;
use yii\mail\MessageInterface;

/**
 * @var MessageInterface $message
 * @var FizLico $fizLico
 * @var Kurs $kurs
 */
$message->setSubject('Успешная регистрация на курс в БРИОП');

switch ($kurs->finansirovanie) {
    case TipFinansirovaniya::BYUDZHET: $finansirovanieTvor = 'бюджетным'; break;
    case TipFinansirovaniya::VNEBYUDZHET: $finansirovanieTvor = 'внебюджетным'; break;
    default: $finansirovanieTvor = 'неизвестным'; break;
}

?>
<p><em><small>Данное письмо сгенерировано автоматически web порталом АУО ДПО БРИОП и не требует ответа!!!</small></em></p>

<p><strong>Здравствуйте, уважаемый(ая) <?= $fizLico->getFio() ?>!</strong></p>
<p>Вы прошли регистрацию на курс «<?= $kurs->nazvanie ?>» с <strong><?= $finansirovanieTvor ?></strong> финансированием.</p>
<p><strong>Срок проведения</strong>: <?= $kurs->srokProvedeniyaFormatted ?></p>
<p><strong>Место проведения</strong>: 670000, г. Улан-Удэ, ул. Советская, 30</p>
