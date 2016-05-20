<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use app\enums\EtapObrazovaniya;
use app\entities\Organizaciya;

/* @var $this yii\web\View */
/* @var $searchModel app\models\organizaciya\OrganizaciyaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Справочник организаций';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organizaciya-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новая организация', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

        'columns' => [
            'nazvanie',
            //'adres_adresnyj_objekt',
            'organizaciyaAdres',
            'adres_dom',
            //'etapy_obrazovaniya',
            [
                'attribute' => 'etapyObrazovaniyaSpisok',
                'filter' => EtapObrazovaniya::namesMap(),
            ],
            'obschij:boolean',
            'vedomstvoNazvanie',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
