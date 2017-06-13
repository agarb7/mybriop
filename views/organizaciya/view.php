<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\organizaciya\Organizaciya */

$flash = \Yii::$app->session->getAllFlashes();
if ($flash){
    $js = '';
    foreach ($flash as $k => $v) {
        $js .= 'bsalert("'.$v.'","'.$k.'","top");'."\n";
    }
    $this->registerJS('$(function(){'.$js.'})');
}

$this->title = $model->nazvanie;
$this->params['breadcrumbs'][] = ['label' => 'Organizaciyas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizaciya-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить организацию?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Отправить в архив', ['archive', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите отправить организацию в архив?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nazvanie',
            'organizaciyaAdres',
            'etapyObrazovaniyaSpisok',
            'obschij:boolean',
            'vedomstvoNazvanie',
        ],
    ]) ?>
</div>
