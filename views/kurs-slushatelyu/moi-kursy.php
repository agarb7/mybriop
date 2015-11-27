<?php

use app\entities\KursExtended;
use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var $this View
 */

$this->title  = 'БРИОП: Мои курсы';
?>

<?= $this->render('_spisok-kursov', [
    'provider' => new ActiveDataProvider([
        'query' => KursExtended::findMyAsSlushatel(),
        'sort' => false
    ])
]) ?>
