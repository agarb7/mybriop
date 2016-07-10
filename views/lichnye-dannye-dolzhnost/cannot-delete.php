<?php
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\helpers\Url;

Alert::begin([
    'options' => [
        'class' => 'alert-warning',
        'closeButton' => false
    ],
])
?>
Действие откланено.
<br>Удалить невозможно, т.к. эту должность вы указали при записи на курсы.
<?php Alert::end() ?>
<?= Html::a(
    'Ok',
    Url::to(Yii::$app->request->referrer),
    ['class' => 'btn btn-default']) ?>

