<?php
use app\entities\FizLico;
use app\entities\Kurs;
use app\helpers\Val;
use yii\data\DataProviderInterface;
use yii\grid\GridView;

/**
 * @var $data DataProviderInterface
 * @var $model Kurs
 */
?>
<?= GridView::widget([
    'caption' =>  "Слушатели курса «{$model->nazvanie}»",
    'dataProvider' => $data,
    'layout' => "{items}",
    'options' => ['class' => 'spisok-slushatelej'],
    'tableOptions' => ['class' => 'table'],
    'columns' => [
        [
            'header' => 'ФИО',
            'value' => 'fio'
        ],
        [
            'header' => 'Работа',
            'format' => 'ntext',
            'value' => function ($fizLico) {
                /**
                 * @var $fizLico FizLico
                 */
                $ret = [];

                foreach ($fizLico->rabotyFizLicaRel as $rabota) {
                    $rabota_part = Val::of($rabota, 'organizaciyaRel', 'nazvanie');

                    $dolzhnosti = $rabota->dolzhnostiFizLicaNaRaboteRel;
                    if ($dolzhnosti) {
                        $dolzhnosti_ar = [];

                        foreach ($dolzhnosti as $dolzhnost)
                            $dolzhnosti_ar[] = Val::of($dolzhnost, 'dolzhnostRel', 'nazvanie');

                        $dolzhnosti_part = ' (' . implode(', ', $dolzhnosti_ar) . ')';
                    } else {

                        $dolzhnosti_part = '';
                    }

                    $ret[] = $rabota_part . $dolzhnosti_part;
                }

                return implode("\n,", $ret);
            }
        ]
    ]
]) ?>
