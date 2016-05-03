<?php
namespace app\upravlenie_kursami\raspisanie\widgets;

use app\helpers\SqlType;
use DateTime;
use DateInterval;

use Yii;

use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

use app\upravlenie_kursami\raspisanie\models\Zanyatie;
use app\upravlenie_kursami\raspisanie\helpers\ZanyatieTime;
use app\upravlenie_kursami\raspisanie\data\DayData;
use app\upravlenie_kursami\raspisanie\models\Day;

class ZanyatieGrid extends Widget
{
    /**
     * @var DayData
     */
    public $data;

    /**
     * @var array
     */
    public $auditorii;

    /**
     * @var array
     */
    public $prepodavateli;

    /**
     * @var string TemaPicker ID
     */
    public $temaPickerSelector;

    /**
     * @var string|array The route of the action
     */
    public $zanyatieUpdateAction;

    /**
     * @var string|array The route of the action
     */
    public $zanyatieDeleteAction;

    /**
     * @var string[]
     */
    private static $_columnCaptions = [
        'Дата',
        'Время',
        'Тема',
        'Вид занятия',
        'Преподаватель',
        'Аудитория',
        ''
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->data instanceof DayData)
            throw new InvalidConfigException;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();

        if ($this->data->getFirstDate() === null || $this->data->getLastDate() === null)
            return $this->renderDatesNotSet();

        return $this->renderGrid();
    }

    /**
     * @return string
     */
    private function renderGrid()
    {
        return Html::tag(
            'table',
            $this->renderHeader() . "\n" . $this->renderBodies(),
            [
                'id' => $this->getId(),
                'class' => 'zanyatie-grid table table-bordered'
            ]
        );
    }

    /**
     * @return string
     */
    private function renderHeader()
    {
        $cols = array_map(
            function ($col) {return Html::tag('th', $col);},
            self::$_columnCaptions
        );

        $row = Html::tag('tr', implode('', $cols));

        return Html::tag('thead', $row);
    }

    /**
     * @return string
     */
    private function renderBodies()
    {
        $result = [];

        $days = array_combine($this->data->getKeys(), $this->data->getModels());

        $lastDate = new DateTime($this->data->getLastDate());
        $p1d = new DateInterval('P1D');
        for ($date = new DateTime($this->data->getFirstDate());
             $date <= $lastDate;
             $date->add($p1d))
        {
            $sqlDate = SqlType::encodeDate($date);
            $day = isset($days[$sqlDate])
                ? $days[$sqlDate]
                : ['data' => $sqlDate];

            $zanyatiya = ArrayHelper::getValue($day, 'zanyatiya');

            $result[] = $this->renderDay($day, $zanyatiya);
        }

        return implode("\n", $result);
    }

    /**
     * @param Day|array $day
     * @param Zanyatie[]|null $zanyatiya
     * @return string
     */
    private function renderDay($day, $zanyatiya)
    {
        $dayData = ArrayHelper::getValue($day, 'data');
        $rows = [];

        for ($nomer = 1; $nomer <= Day::$zanyatiyaMax; ++$nomer) {
            $zanyatie = ArrayHelper::getValue($zanyatiya, $nomer);
            $rows[] = $this->renderRow($dayData, $nomer, $zanyatie);
        }

        return Html::tag(
            'tbody',
            "\n" . implode("\n", $rows) . "\n"
        );
    }

    /**
     * @param string $dayData
     * @param integer $nomer
     * @param Zanyatie|null $zanyatie
     * @return string
     */
    private function renderRow($dayData, $nomer, $zanyatie)
    {
        $cols = '';

        if ($nomer == 1)
            $cols .= $this->renderDataCell($dayData);

        $renderText = [$this,'renderTextContent'];
        $renderDropDown = [$this, 'renderDropDownContent'];

        $cols .= $this->renderNomerCell($nomer)
            . $this->renderBlankCell($zanyatie)
            . $this->renderContentCell($zanyatie, $renderText, 'tema_nazvanie_chast')
            . $this->renderContentCell($zanyatie, $renderText, 'tema_tip_raboty_nazvanie')
            . $this->renderContentCell($zanyatie, $renderDropDown, 'prepodavatel', $this->prepodavateli)
            . $this->renderContentCell($zanyatie, $renderDropDown, 'auditoriya', $this->auditorii)
            . $this->renderResetButtonCell($zanyatie);

        return Html::tag(
            'tr',
            $cols,
            [
                'data' => [
                    'data' => $dayData,
                    'nomer' => $nomer
                ]
            ]
        );
    }

    /**
     * @param string $dayData
     * @return string
     */
    private function renderDataCell($dayData)
    {
        $format = Yii::$app->formatter->dateFormat . ' E';

        return Html::tag(
            'td',
            Yii::$app->formatter->asDate($dayData, $format),
            ['rowspan' => Day::$zanyatiyaMax]
        );
    }

    /**
     * @param integer $nomer
     * @return string
     */
    private function renderNomerCell($nomer)
    {
        return Html::tag(
            'td',
            ZanyatieTime::interval($nomer),
            ['class' => 'tema-picking-cell']
        );
    }

    /**
     * @param Zanyatie|null $zanyatie
     * @return string
     */
    private function renderBlankCell($zanyatie)
    {
        $options = [
            'colspan' => count(self::$_columnCaptions) - 2,
            'style' => !$zanyatie ? null : 'display:none',
            'class' => 'tema-picking-cell blank-cell'
        ];

        return Html::tag('td', '', $options);
    }

    /**
     * @param Zanyatie|null $zanyatie
     * @param callable|string $content Callable signature is
     *  function ($zanyatie, $attribute, $arg1, ...). Next params will be $attribute, $arg1, etc...
     *
     * @return string
     */
    private function renderContentCell($zanyatie, $content)
    {
        if (is_callable($content)) {
            $args = array_slice(func_get_args(), 2);
            array_unshift($args, $zanyatie);
            $content = call_user_func_array($content, $args);
        }

        return Html::tag('td', $content, [
            'style' => $zanyatie ? null : 'display:none',
            'class' => 'data-cell'
        ]);
    }

    /**
     * @param string $attribute
     * @return array
     */
    private function contentDataOption($attribute)
    {
        return ['data-attribute' => $attribute];
    }
    
    private function renderTextContent($zanyatie, $attribute)
    {
        return Html::tag(
            'span',
            ArrayHelper::getValue($zanyatie,$attribute),
            $this->contentDataOption($attribute)
        );
    }
    
    private function renderDropDownContent($zanyatie, $attribute, $items)
    {
        return Html::dropDownList(
            '',
            ArrayHelper::getValue($zanyatie,$attribute),
            $items,
            ArrayHelper::merge(
                $this->contentDataOption($attribute),
                ['class' => 'form-control']
            )
        );
    }

    /**
     * @param Zanyatie|null $zanyatie
     * @return string
     */
    private function renderResetButtonCell($zanyatie)
    {
        $content = Html::a(
            '&times;',
            '#',
            ['class' => 'btn btn-primary zanyatie-delete-btn']
        );

        return $this->renderContentCell($zanyatie, $content);
    }

    /**
     * @return string
     */
    private function renderDatesNotSet()
    {
        return Html::tag(
            'div',
            'Нужно установить даты начала и конца занятий',
            ['class' => 'dates-not-set']
        );
    }

    /**
     * Register JavaScript on view
     */
    private function registerClientScript()
    {
        ZanyatieGridAsset::register($this->getView());

        $options = Json::htmlEncode([
            'temaPicker' => $this->temaPickerSelector,
            'zanyatieUpdateUrl' => Url::to($this->zanyatieUpdateAction),
            'zanyatieDeleteAction' => Url::to($this->zanyatieDeleteAction)
        ]);

        $this->getView()->registerJs('$("#' . $this->getId() . '").zanyatieGrid(' . $options . ');');
    }
}