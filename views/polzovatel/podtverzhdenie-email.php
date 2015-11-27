<?php

use yii\helpers\Html;
use app\models\polzovatel\PodtverzhdenieEmail;

/**
 * @var PodtverzhdenieEmail $model
 */

if (!$model->hasErrors()):
    $this->title = 'БРИОП: e-mail подтверждён';?>
    <p>Вы <strong>успешно подтвердили e-mail</strong>. Теперь вы можете войти на сайт используя ваш логин и пароль.</p>
<?php else:
    $this->title = 'БРИОП: e-mail НЕ подтверждён';?>
    <p><strong>E-mail НЕ подтверждён</strong>.</p>
    <?= Html::errorSummary($model); ?>
<?php endif; ?>
