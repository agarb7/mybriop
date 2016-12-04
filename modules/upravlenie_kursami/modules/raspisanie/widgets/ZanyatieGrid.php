<?php
namespace app\upravlenie_kursami\raspisanie\widgets;

use app\enums2\FormaZanyatiya;
use app\enums2\StatusRaspisaniyaKursa;
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
use app\upravlenie_kursami\raspisanie\data\DayData;
use app\upravlenie_kursami\raspisanie\models\Day;

class ZanyatieGrid extends Widget
{

    /**
     * @var KursForm
     */
    public $kurs;

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
     * @var string
     */
    public $prepodavatelPeresechenieModalSelector;

    /**
     * @var string[]
     */
    private static $_headers = [
        ['','date-header'],
        ['Время', 'time-header'],
        ['Тема', 'tema-header'],
        ['Вид занятия', 'vid-header'],
        ['Форма занятия', 'forma-header'],
        ['Преподаватель', 'prepodavatel-header'],
        ['Аудитория', 'auditoriya-header'],
        ['', 'action-header']
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
                'class' => 'zanyatie-grid'
            ]
        );
    }

    /**
     * @return string
     */
    private function renderHeader()
    {
        $cols = array_map(
            function ($col) {
                return Html::tag('th', $col[0], ['class' => $col[1]]);
            },
            self::$_headers
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
        $renderPrepodavatel = [$this, 'renderPrepodavatelContent'];
        $renderAuditoriya = [$this, 'renderAuditoriyaContent'];
        $renderTemaContent = [$this, 'renderTemaContent'];

        $cols .= $this->renderTimeCell($nomer)
            . $this->renderBlankCell($zanyatie)
            . $this->renderContentCell($zanyatie, $renderTemaContent)
            . $this->renderContentCell($zanyatie, $renderText, 'tema_tip_raboty_nazvanie');
        if ($this->kurs->status_raspisaniya == StatusRaspisaniyaKursa::REDAKTIRUETSYA) {
            $cols .= $this->renderContentCell($zanyatie, $renderDropDown, 'forma', FormaZanyatiya::names())
                    . $this->renderContentCell($zanyatie, $renderPrepodavatel, 'prepodavatel', $this->prepodavateli)
                    . $this->renderContentCell($zanyatie, $renderAuditoriya, 'auditoriya_id', $this->auditorii, 'auditoriya_nazvanie')
                    . $this->renderResetButtonCell($zanyatie);
        }
        else {
            $cols .= $this->renderTextCell($zanyatie ? FormaZanyatiya::names()[$zanyatie->forma] : '')
                    . $this->renderTextCell($zanyatie ? $this->prepodavateli[$zanyatie->prepodavatel] : '')
                    . $this->renderTextCell($zanyatie && $zanyatie->auditoriya_id ? $this->auditorii[$zanyatie->auditoriya_id] : '', ['class' => 'center'])
                    .'<td>&nbsp;</td>';
        }

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
     * Return td with text
     *
     * @param $text string
     * @param $style array
     * @return string
     */
    private function renderTextCell($text, $params = []){
        return Html::tag('td', $text, $params);
    }


    /**
     * @param string $dayData
     * @return string
     */
    private function renderDataCell($dayData)
    {
        $formatter = Yii::$app->formatter;

        $date = $formatter->asDate($dayData, $formatter->dateFormat);

        $nDayOfWeek = (new DateTime($dayData))->format('N');

        $day = Html::tag(
            'span',
            $formatter->asDate($dayData, 'E'),
            ['class' => "day-of-week day-of-week-$nDayOfWeek"]
        );

        $tdContent = Html::tag('span', $date . $day, ['class' => 'date']);

        return Html::tag('td', $tdContent, [
            'rowspan' => Day::$zanyatiyaMax,
            'class' => 'date-cell'
        ]);
    }

    /**
     * @param integer $nomer
     * @return string
     */
    private function renderTimeCell($nomer)
    {
        return Html::tag(
            'td',
            Yii::$app->formatter->asZanyatieTimeInterval($nomer),
            ['class' => 'tema-picking-cell time-cell']
        );
    }

    /**
     * @param Zanyatie|null $zanyatie
     * @return string
     */
    private function renderBlankCell($zanyatie)
    {
        $options = [
            'colspan' => count(self::$_headers) - 2,
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
     * @param string $attribute
     * @param array $items
     * @return string
     */
    private function renderPrepodavatelContent($zanyatie, $attribute, $items)
    {
        $btnClass = 'btn btn-danger zanyatie-prepodvatel-peresechenie-btn';
        $dropDownClass = 'form-control';

        if ($zanyatie && $zanyatie->prepodavatel_peresechenie) {
            $btnClass .= ' peresechenie-est';
            $dropDownClass .= ' peresechenie-est';
        }

        return
            Html::dropDownList(
                '',
                ArrayHelper::getValue($zanyatie,$attribute),
                $items,
                $this->contentDataOption($attribute) + ['class' => $dropDownClass]
            )
            . Html::a(
                '!',
                '#',
                $this->contentDataOption('prepodavatel_peresechenie') + ['class' => $btnClass]
            );
    }


    /**
     * @param Zanyatie $zanyatie
     * @param string $idAttribute
     * @param array $items
     * @param string $nazvanieAttribute
     * @return string
     */
    private function renderAuditoriyaContent($zanyatie, $idAttribute, $items, $nazvanieAttribute)
    {
        $content = Html::dropDownList(
                '',
                ArrayHelper::getValue($zanyatie, $idAttribute),
                $items,
                ArrayHelper::merge(
                    $this->contentDataOption($idAttribute),
                    ['class' => 'form-control']
                )
            )
            . Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-pencil']),
                '#',
                ['class' => 'btn btn-default zanyatie-auditoriya-write-btn']
            )
            . Html::textInput(
                '',
                ArrayHelper::getValue($zanyatie, $nazvanieAttribute),
                ArrayHelper::merge(
                    $this->contentDataOption($nazvanieAttribute),
                    ['class' => 'form-control']
                )
            )
            . Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-book']),
                '#',
                ['class' => 'btn btn-default zanyatie-auditoriya-select-btn']
            );

        $containerClass = 'zanyatie-auditoriya-container';
        if (ArrayHelper::getValue($zanyatie, $nazvanieAttribute))
            $containerClass .= ' zanyatie-auditoriya-write';

        return Html::tag(
            'div',
            $content,
            ['class' => $containerClass]
        );
    }

    private function renderTemaContent($zanyatie)
    {
        $inPotokHidden = ArrayHelper::getValue($zanyatie, 'isPotok') ? null: 'hidden';
        $nazv =  $this->renderTextContent($zanyatie, 'deduced_nazvanie');
        $inPotok = Html::tag('em', 'в потоке', [
            'class' => ['label','label-warning', 'label-in-potok', $inPotokHidden],
            'data-attribute' => 'is_potok'
        ]);
        return $nazv . $inPotok;
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
            'prepodavatelPeresechenieModal' => $this->prepodavatelPeresechenieModalSelector,
            'zanyatieUpdateUrl' => Url::to($this->zanyatieUpdateAction),
            'zanyatieDeleteAction' => Url::to($this->zanyatieDeleteAction)
        ]);

        $this->getView()->registerJs('$("#' . $this->getId() . '").zanyatieGrid(' . $options . ');');
    }
}