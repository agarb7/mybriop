<?php
namespace app\upravlenie_kursami\raspisanie\widgets;

use Yii;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use app\components\Formatter;
use app\records\RazdelKursa;

use app\upravlenie_kursami\models\FizLico;

use app\upravlenie_kursami\raspisanie\models\PodrazdelKursa;
use app\upravlenie_kursami\raspisanie\models\ChastTemy;
use app\upravlenie_kursami\raspisanie\models\TemaFilter;

class TemaPickerContent extends Widget
{
    /**
     * @var RazdelKursa[]
     */
    public $data;

    /**
     * @var TemaFilter
     */
    public $filter;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->renderItems($this->data, [$this, 'renderRazdel']);
    }

    /**
     * @param RazdelKursa $razdel
     * @return string
     */
    private function renderRazdel($razdel) 
    {
        $items = $this->renderItems($razdel->podrazdely_rel, [$this, 'renderPodrazdel']);
        if (!$items)
            return '';

        $caption = ArrayHelper::getValue($razdel, 'nazvanie_rel.nazvanie');
        $header = Html::tag(
            'h4',
            $this->renderNumbered($caption, [$razdel->nomer])
        );

        $content = $header . "\n" . $items;

        return Html::tag('div', $content, ['class' => 'tema-picker-razdel']);
    }

    /**
     * @param PodrazdelKursa $podrazdel
     * @return string
     */
    private function renderPodrazdel($podrazdel)
    {
        if (!$this->filter->filterPodrazdel($podrazdel))
            return '';

        $items = $this->renderItems($podrazdel->unused_chasti_tem, [$this, 'renderChastTemy']);
        if (!$items)
            return '';

        $numbers = [
            ArrayHelper::getValue($podrazdel, 'razdel_rel.nomer'),
            $podrazdel->nomer
        ];

        $header = Html::tag(
            'h5',
            $this->renderNumbered($podrazdel->nazvanie, $numbers)
        );

        $content = $header . "\n" . $items;

        return Html::tag('div', $content, ['class' => 'tema-picker-podrazdel']);
    }

    /**
     * @param ChastTemy $chastTemy
     * @return string
     */
    private function renderChastTemy($chastTemy)
    {
        $tema = $chastTemy->tema;

        if (!$this->filter->filterTema($tema))
            return '';

        $numbers = [
            ArrayHelper::getValue($tema, 'podrazdel_rel.razdel_rel.nomer'),
            ArrayHelper::getValue($tema, 'podrazdel_rel.nomer'),
            $tema->nomer
        ];
        
        $nazvanieDiv = Html::tag(
            'div',
            $this->renderNumbered($chastTemy->tema_nazvanie_chast, $numbers),
            ['class' => 'col-md-7']
        );

        /* @var $formatter Formatter */
        $formatter = Yii::$app->formatter;

        /* @var $prepodavatel FizLico */
        $prepodavatel = $tema->prepodavatel_fiz_lico_rel;

        $podrazdelenieSpan = Html::tag(
            'span',
            ArrayHelper::getValue($prepodavatel, 'pervoe_strukturnoe_podrazdelenie_briop.nazvanie'),
            ['class' => 'podrazdelenie']
        );

        $prepodavatelDiv = Html::tag(
            'div',
            $formatter->asFizLico($prepodavatel) . "\n" . $podrazdelenieSpan,
            ['class' => 'col-md-2']
        );

        $vidZanyatiyaDiv = Html::tag(
            'div',
            ArrayHelper::getValue($tema, 'tip_raboty_rel.nazvanie'),
            ['class' => 'col-md-2']
        );

        $nedelyaDiv = Html::tag(
            'div',
            $formatter->asInteger($tema->nedelya) . ' нед.',
            ['class' => 'col-md-1']
        );

        $options = [
            'class' => 'row tema-picker-item',
            'data-id' => $tema->id,
            'data-chast' => $chastTemy->chast
        ];
        
        return Html::tag(
            'div',
            $nazvanieDiv . $vidZanyatiyaDiv . $prepodavatelDiv . $nedelyaDiv,
            $options
        );
    }

    /**
     * @param string $text
     * @param integer[] $numbers
     * @return string
     */
    private function renderNumbered($text, $numbers)
    {
        return implode('.', $numbers) . '. ' . $text;
    }

    /**
     * @param mixed $array
     * @param callable $renderer
     * @return mixed
     */
    private function renderItems($array, $renderer)
    {
        $pieces = [];

        foreach ($array as $item)
            $pieces[] = $renderer($item);

        return implode("\n", array_filter($pieces));
    }
}