<?php
namespace app\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveField;
use yii\widgets\ActiveForm;


class SwitchingFields extends Widget
{
    public $commonOptions;
    public $field1Options;
    public $field2Options;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $config1 = ArrayHelper::merge($this->commonOptions, $this->field1Options);
        $config2 = ArrayHelper::merge($this->commonOptions, $this->field2Options);

        $field1 = self::createField($config1, $config2['attribute'], true);
        $field2 = self::createField($config2, $config1['attribute'], false);

        return $field1 . "\n" . $field2;
    }

    /**
     * @param $config
     * @param $otherAttribute
     * @param $isDefault
     * @throws \Exception
     * @return ActiveField
     */
    private static function createField($config, $otherAttribute, $isDefault)
    {
        /**
         * @var ActiveForm $form
         */
        $form = $config['form'];

        $model = $config['model'];
        $attribute = $config['attribute'];

        if (isset($model[$attribute]))
            $disabled = false;
        elseif (!isset($model[$otherAttribute]))
            $disabled = !$isDefault;
        else
            $disabled = true;

        $switch = FieldSwitch::widget([
            'model' => $model,
            'fromAttribute' => $attribute,
            'toAttribute' => $otherAttribute,
            'introText' => $config['switchIntroText'],
            'linkText' => $config['switchLinkText']
        ]);

        $options = $config['options'];
        $options['template'] = strtr($options['template'], ['{switch}' => $switch]);

        if ($disabled) {
            $options['options']['style'] = 'display:none';
            $options['inputOptions']['disabled'] = true;
        }

        $field = $form->field($model, $attribute, $options);

        if (isset($config['widgetClass'])) {
            if ($disabled) {
                $widgetConfigDisabled = isset($config['widgetConfigDisabled'])
                    ? $config['widgetConfigDisabled']
                    : ['options' => ['disabled' => true]];

                $widgetConfig = ArrayHelper::merge($config['widgetConfig'], $widgetConfigDisabled);
            } else {
                $widgetConfig = $config['widgetConfig'];
            }
            $field = $field->widget($config['widgetClass'], $widgetConfig);

        } elseif (isset($config['method'])) {
            $field = call_user_func_array(
                [$field, $config['method']],
                isset($config['arguments']) ? $config['arguments'] : []
            );
        }

        return $field;
    }
}