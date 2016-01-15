<?php

use app\entities\KursExtended;
use app\widgets\PlanProspektGodPanel;
use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var $this View
 * @var $data ActiveDataProvider
 */

$this->title  = 'БРИОП: Мои курсы';
?>
<?= PlanProspektGodPanel::widget() ?>
<?= $this->render('_spisok-kursov', ['provider' => $data]) ?>
