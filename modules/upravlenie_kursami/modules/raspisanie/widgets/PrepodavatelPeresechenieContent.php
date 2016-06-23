<?php
namespace app\modules\upravlenie_kursami\modules\raspisanie\widgets;

use app\components\Formatter;
use Yii;

use yii\base\Widget;
use yii\data\DataProviderInterface;
use yii\helpers\Html;

use app\upravlenie_kursami\raspisanie\models\Zanyatie;
use app\upravlenie_kursami\raspisanie\models\Kurs;

class PrepodavatelPeresechenieContent extends Widget
{
    /**
     * @var Zanyatie
     */
    public $zanyatie;

    /**
     * @var DataProviderInterface
     */
    public $dataProvider;

    /**
     * @inheritdoc
     */
    public function run()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        $prepodavatel = $formatter->asFizLico($this->zanyatie->prepodavatel_rel);
        $date = $formatter->asDate($this->zanyatie->data);
        $interval = $formatter->asZanyatieTimeInterval(
            $this->zanyatie->nomer,
            Formatter::ZANYATIE_TIME_INTERVAL_FORMAT_FROM_TO
        );

        return
            Html::tag('p', "$date $interval преподаватель $prepodavatel занят в:")
            . "\n" . $this->renderKursy($this->dataProvider->getModels());
    }

    /**
     * @param Kurs[] $kursy
     * @return string
     */
    private function renderKursy($kursy)
    {
        return Html::ul(
            array_map(function (Kurs $item) {
                /* @var $formatter Formatter */
                $formatter = Yii::$app->formatter;

                $rukovoditel = Html::encode($formatter->asFizLico($item->rukovoditel_rel));

                return
                    Html::encode($item->nazvanie) . ' '
                    . Html::tag('span', "(руководитель: $rukovoditel)", ['class' => 'rukovoditel']);
            }, $kursy),
            ['encode' => false]
        );
    }
}