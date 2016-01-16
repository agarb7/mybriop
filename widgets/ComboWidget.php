<?php
namespace app\widgets;

use kartik\select2\Select2;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;
use Yii;

class ComboWidget extends InputWidget
{
    // if changed, change also in app.comboWidget.js
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

        $clientValue = self::toClientFormat($this->model->{$this->attribute});
        if ($clientValue === false)
            throw new InvalidConfigException;

        $this->model->{$this->attribute} = $clientValue;

        if (!isset($this->select2Config['pluginOptions']['allowClear'])) {
            $this->select2Config['pluginOptions']['placeholder'] = '';
            $this->select2Config['pluginOptions']['allowClear'] = true;
        }

        if (!isset($this->select2Config['options']))
            $this->select2Config['options'] = null;

        $this->setDataTarget($this->select2Config['options']);
        Html::addCssClass($this->select2Config['options'], 'combowidget-select2');

        if (!isset($this->select2Config['value']))
            $this->select2Config['value'] = $this->inputValue(self::STATE_LIST);

        // ugly workaround (InputWidget must have name or model)
        $this->select2Config['name'] = Html::getInputId($this->model, $this->attribute) . '-ignored-combowidget-select2';

        $this->setDataTarget($this->switchOptions);
        Html::addCssClass($this->switchOptions, 'combowidget-switch');

        $this->setDataTarget($this->textInputOptions);
        Html::addCssClass($this->textInputOptions, 'combowidget-textinput');
    }

    public function setData($data)
    {
        $this->select2Config['data'] = $data;
    }

    public function getData()
    {
        return ArrayHelper::getValue($this->select2Config, 'data', []);
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
        if ($this->state() !== self::STATE_TEXT)
            Html::addCssStyle($this->textInputOptions, 'display:none');

        return Html::textInput(null, $this->inputValue(self::STATE_TEXT), $this->textInputOptions);
    }

    private function renderSelect2()
    {
        return Html::tag(
            'span',
            Select2::widget($this->select2Config),
            $this->state() !== self::STATE_LIST ? ['style' => 'display:none'] : []
        );
    }

    private function renderSwitch()
    {
        $text = $this->switchTexts[$this->state()];

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

    /**
     * @param $value
     * @return bool|string
     */
    private static function toClientFormat($value)
    {
        if (is_string($value))
            return $value;

        if (!$value)
            $res = [self::STATE_LIST, null];
        elseif (isset($value['id']))
            $res = [self::STATE_LIST, $value['id']];
        elseif (isset($value['nazvanie']))
            $res = [self::STATE_TEXT, $value['nazvanie']];
        else
            return false;

        return Json::htmlEncode($res);
    }

}