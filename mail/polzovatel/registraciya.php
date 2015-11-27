<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\mail\MessageInterface;

/**
 * @var MessageInterface $message
 * @var \app\models\polzovatel\Registraciya $model
 * @var \app\entities\Polzovatel $polzovatel
 */
$message->setSubject('Регистрация в БРИОП: информация и подтверждение e-mail');
?>

<strong>Здравствуйте!</strong>
<p>Вы зарегистрированы в БРИОП.</p>
<p>Но для завершения регистрации остался один шаг: подтверждение e-mail.
    Для этого пройдите по <?= Html::a('этой ссылке',Url::to([
        'polzovatel/podtverzhdenie-email',
        'login' => $polzovatel->login,
        'kod' => $polzovatel->kodPodtverzhdeniyaEmail
    ],true)) ?>.</p>

<p>На всякий случай, напоминаем Ваши реквизиты для входа на сайт. </p>
<p><strong>Логин</strong>: <?= $polzovatel->login ?></p>
<p><strong>Пароль</strong>: <?= $model->parol ?></p>
