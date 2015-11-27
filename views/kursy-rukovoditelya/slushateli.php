<?php
use app\entities\FizLico;
use app\entities\Kurs;
use app\enums\StatusZapisiNaKurs;
use app\helpers\Val;
use app\models\kursy_rukovoditelya\ZapisModel;
use yii\data\DataProviderInterface;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\web\YiiAsset;

/**
 * @var $data DataProviderInterface
 * @var $kursRecord Kurs
 * @var $model ZapisModel
 */
?>
<?= GridView::widget([
    'caption' =>  "Слушатели курса «{$kursRecord->nazvanie}»",
    'dataProvider' => $data,
    'layout' => "{items}",
    'options' => ['class' => 'spisok-slushatelej'],
    'tableOptions' => ['class' => 'table'],
    'columns' => [
        [
            'class' => SerialColumn::className()
        ],
        [
            'header' => 'ФИО',
            'value' => 'fio'
        ],
        [
            'header' => 'Личные контакты',
            'format' => 'html',
            'value' => function ($fizLico) {
                $parts = [];

                if ($fizLico->telefon)
                    $parts[] = Yii::$app->formatter->asHtmlTelefon($fizLico->telefon);

                if ($fizLico->email)
                    $parts[] = Yii::$app->formatter->asEmail($fizLico->email);

                return implode("<br>", $parts);
            }
        ],
        [
            'header' => 'Работа',
            'format' => 'html',
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

                    $tel = $rabota->telefon;
                    $tel_part = $tel
                        ? ' ' . Yii::$app->formatter->asHtmlTelefon($tel)
                        : '';

                    $ret[] = $rabota_part . $dolzhnosti_part . $tel_part;
                }

                return implode(",<br>", $ret);
            }
        ],
        [
            'format' => 'raw',
            'value' => function ($fizLico) use ($kursRecord, $model) {
                /**
                 * @var $fizLico FizLico
                 */
                $status = StatusZapisiNaKurs::asValue($fizLico->statusKursaGdeSlushatel);

                YiiAsset::register($this);

                if ($status === StatusZapisiNaKurs::ZAPIS) {
                    $text = 'Отменить запись';
                    $class = 'btn btn-info';
                    $new_status = StatusZapisiNaKurs::OTMENENO_RUKOVODITELEM;
                } elseif ($status === StatusZapisiNaKurs::OTMENENO_RUKOVODITELEM) {
                    $text = 'Записать снова';
                    $class = 'btn btn-warning';
                    $new_status = StatusZapisiNaKurs::ZAPIS;
                } else {
                    return null;
                }

                return Html::a($text, '', [
                    'class' => $class,
                    'data' => [
                        'method' => 'post',
                        'params' => [
                            Html::getInputName($model, 'fizLicoHashids') => $fizLico->hashids,
                            Html::getInputName($model, 'status') => $new_status
                        ]
                    ]
                ]);
            }
        ]
    ]
]) ?>
