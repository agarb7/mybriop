<?php

/**
 * @var \app\models\kadry\Registraciya $model
 */

use app\models\strukturnoe_podrazdelenie\StrukturnoePodrazdelenie;
use app\enums2\TipDogovoraRaboty;
use yii\helpers\Html;

$this->title = 'Регистрация нового пользователя';

?>

<?php if (empty($model->fizLicoId)):?>
    <p>Вы успешно зарегистрировали нового сотруника:</p>
    <?= Html::tag('h4',$model['familiya'].' '.$model['imya'].' '.$model['otchestvo']); ?>
    <?= Html::tag('p','Личный телефон: '.$model['telefon']); ?>
    <?= Html::tag('p','e-mail: '.$model['email']); ?>
    <p> На указанный выше электронный адрес сотрудника отправлено письмо c логином <em>(<?=$model->login?>)</em> и паролем
    <em>(<?=$model->parol?>)</em>.</p>
<? else: ?>
    <p>Ранее зарегистрированный пользователь системы:</p>
    <?= Html::tag('h4',$fizlico['familiya'].' '.$fizlico['imya'].' '.$fizlico['otchestvo']); ?>
    <?= Html::tag('p','Дата рождения: '.$fizlico['data_rozhdeniya']); ?>
    <?= Html::tag('p','Личный телефон: '.$fizlico['telefon']); ?>
    <?= Html::tag('p','e-mail: '.$fizlico['email']); ?>
    <p>Принят в <em>"<?= StrukturnoePodrazdelenie::findOne(['id' => $model['strukturnoePodrazdelenie']])->nazvanie ?>"</em>
    на основании <em>"<?= TipDogovoraRaboty::getName($model['tipDogovora'])?>"</em>.</p>
<? endif; ?>
