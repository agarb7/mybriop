<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\mail\MessageInterface;

/**
 * @var MessageInterface $message
 * @var \app\models\polzovatel\Registraciya $model
 * @var \app\entities\Polzovatel $polzovatel
 */
$message->setSubject('Регистрация в БРИОП: информация');
?>

<strong>Здравствуйте!</strong>
<p>Вы зарегистрированы администратором автоматизированной информационной системы "БРИОП".</p>
<p>Ваши реквизиты для входа в личный кабинет <a href="http://my.briop.ru">АИС "БРИОП"</a> </p>
<p><strong>Логин</strong>: <?= $polzovatel->login ?></p>
<p><strong>Пароль</strong>: <?= $model->parol ?></p>
