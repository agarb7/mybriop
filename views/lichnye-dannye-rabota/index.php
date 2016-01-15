<?php
use app\entities\RabotaFizLica;
use app\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * @var $data ActiveDataProvider
 */
?>
<?= GridView::widget([
    'dataProvider' => $data,
    'layout' => '{items}',
    'columns' => [
        'organizaciyaRel.nazvanie:text:Учреждение',
        'org_tip:orgTipRaboty:Совместительство',
//        'dolya_stavki:text:Доля ставки',
        'telefon:htmlTelefon:Телефон',
        [
            'label' => 'Должности',
            'format' => 'html',
            'value' => function ($model) {
                /* @var $model RabotaFizLica */
                $text = implode(', ', ArrayHelper::getColumn($model->dolzhnostiFizLicaNaRaboteRel, 'dolzhnostRel.nazvanie'));
                return $text
                    ? Html::a($text, ['/lichnye-dannye-dolzhnost/index', 'rabota' => $model->hashids])
                    : Html::a('добавить должность', ['/lichnye-dannye-dolzhnost/create', 'rabota' => $model->hashids]);
            }
        ],
        [
            'class' => ActionColumn::className(),
            'template' => '{update} {delete}',
        ],
    ]
]) ?>
<?= Html::a('Добавить работу', ['create'], ['class' => 'btn btn-primary']) ?>