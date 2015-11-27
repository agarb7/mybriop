<?php

use app\enums\TipKursa;
use app\models\kurs_slushatelyu\SpisokKursovFilterForm;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var SpisokKursovFilterForm $filterModel
 * @var ActiveDataProvider $provider
 * @var mixed $tip Tip kursa
 * @var View $this
 */

$this->title  = ArrayHelper::getValue([
    TipKursa::PK => 'Курсы повышения квалификации',
    TipKursa::PP => 'Курсы профессиональной переподготовки',
    TipKursa::PO => 'Курсы профессионального обучения'
], $tip);

Yii::$app->user->returnUrl = Url::current();

?>

<h2><?=$this->title?></h2>

<?= $this->render('_spisok-kursov-filter', ['model' => $filterModel]) ?>

<?= $this->render('_spisok-kursov', compact('provider')) ?>
