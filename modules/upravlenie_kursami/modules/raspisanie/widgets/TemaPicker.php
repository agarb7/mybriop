<?php
namespace app\upravlenie_kursami\raspisanie\widgets;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

use yii\bootstrap\Modal;

use app\records\Kurs;

class TemaPicker extends Modal
{
    /**
     * @var Kurs
     */
    public $kurs;

    /**
     * @var array
     */
    public $temaIndexAction;

    /**
     * @var array
     */
    public $temaFilterOptionsAction;

    /**
     * @var string
     */
    public $size = self::SIZE_LARGE;

    public function init()
    {
        $this->options['class'] = 'tema-picker fade';
        $this->header = Html::tag('h4', 'Выбор темы');
        $this->footer = $this->renderButtons();

        parent::init();
    }

    public function run()
    {
        $this->registerClientScript();

        echo $this->renderTitle();
        echo $this->renderFilter();
        echo $this->renderContent();
        echo $this->renderNoTemyMessage();

        return parent::run();
    }

    private function renderContent()
    {
        return Html::tag(
            'div',
            '',
            ['class' => 'tema-picker-content']
        );
    }

    private function renderButtons()
    {
        return Html::tag('a', 'Ок', ['class' => 'btn btn-primary ok-btn', 'data-dismiss' => 'modal'])
        . Html::tag('a', 'Отмена', ['class' => 'btn btn-default cancel-btn', 'data-dismiss' => 'modal']);
    }

    private function renderTitle()
    {
        return Html::tag('h3', $this->kurs->nazvanie, ['class' => 'kurs-title']);
    }

    private function renderFilter()
    {
        return $this->render('tema-picker-filter');
    }

    private function renderNoTemyMessage()
    {
        $msg = 'Пусто';

        return Html::tag('div', $msg, ['class' => 'no-temy-message']);
    }

    private function registerClientScript()
    {
        $view = $this->getView();
        TemaPickerAsset::register($view);

        $options = Json::htmlEncode([
            'temaIndexUrl' => Url::to($this->temaIndexAction),
            'temaFilterOptionsUrl' => Url::to($this->temaFilterOptionsAction),
            'filterAttributes' => [
                'podrazdel',
                'prepodavatel_fiz_lico',
                'prepodavatel_strukturnoe_podrazdelenie',
                'nedelya'
            ]
        ]);

        $view->registerJs('$("#' . $this->getId() . '").temaPicker(' . $options . ');');
    }
}