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
Действие откланено. На этой работе есть введённые должности. 
<br>Если вы действительно хотите удалить работу, удалите, пожалуйста, все должности, а затем удалите работу.
<?php Alert::end() ?>
<?= Html::a(
    'Ok',
    Url::to(['index']),
    ['class' => 'btn btn-default']) ?>

