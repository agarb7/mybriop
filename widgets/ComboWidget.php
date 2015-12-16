<?php
namespace app\widgets;

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class ComboWidget extends InputWidget
{
    // if changed, change also in assets
    const STATE_LIST = 1;
    const STATE_TEXT = 2;

    public $textInputOptions = ['class' => 'form-control'];
    public $select2Config = [];
    public $switchOptions = [];

    public $switchTexts = [
        self::STATE_LIST => 'нажмите, чтобы ввести вручную',
        self::STATE_TEXT => 'нажмите, чтобы выбрать из списка'
    ];

    public function init()
    {
        parent::init();

        $value = $this->model->{$this->attribute};
        if ($value === null)
            $this->model->{$this->attribute} = Json::htmlEncode([self::STATE_LIST, null]);

        if (!isset($this->select2Config['options']))
            $this->select2Config['options'] = null;

        // ugly workaround (InputWidget must have name or model)
        $this->select2Config['name'] = Html::getInputId($this->model, $this->attribute) . '-ignored-combowidget-select2';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();

        $res = $this->renderHiddenInput() . "\n"
            . $this->renderTextInput() . "\n"
            . $this->renderSelect2() . "\n"
            . $this->renderSwitch() . "\n";

        return $res;
    }

    private function registerClientScript()
    {
        ComboWidgetAsset::register($this->view);

        $options = Json::htmlEncode(['switchTexts' => $this->switchTexts]);
        $js = "jQuery('#$this->id').appComboWidget($options);";

        $this->view->registerJs($js);
    }

    private function renderHiddenInput()
    {
        return Html::activeHiddenInput($this->model, $this->attribute, ['id' => $this->id]);
    }

    private function renderTextInput()
    {
        Html::addCssClass($this->textInputOptions, 'combowidget-textinput');
        $this->setDataTarget($this->textInputOptions);

        if ($this->state() !== self::STATE_TEXT)
            Html::addCssStyle($this->textInputOptions, 'display:none');

        return Html::textInput(null, $this->inputValue(self::STATE_TEXT), $this->textInputOptions);
    }

    private function renderSelect2()
    {
        $this->select2Config['options'] = ['class' => 'combowidget-select2'];
        $this->setDataTarget($this->select2Config['options']);

        return Html::tag(
            'span',
            Select2::widget($this->select2Config),
            $this->state() !== self::STATE_LIST ? ['style' => 'display:none'] : []
        );
    }

    private function renderSwitch()
    {
        $text = $this->switchTexts[$this->state()];

        Html::addCssClass($this->switchOptions, 'combowidget-switch');
        $this->setDataTarget($this->switchOptions);

        return Html::a($text, '#', $this->switchOptions);
    }

    private function state()
    {
        return $this->decodedValue()[0];
    }

    private function inputValue($state = null)
    {
        $encoded = $this->decodedValue();

        if ($state === null)
            return $encoded[1];

        return $encoded[0] === $state ? $encoded[1] : null;
    }

    private function value()
    {
        return $this->model->{$this->attribute};
    }

    private function decodedValue()
    {
        return Json::decode($this->value());
    }

    private function setDataTarget(&$options)
    {
        $options['data'] = ['target' => $this->id];
    }
}