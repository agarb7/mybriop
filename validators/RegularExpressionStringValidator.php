<?php
namespace app\validators;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\validators\StringValidator;
use Yii;
use yii\validators\ValidationAsset;
use yii\web\JsExpression;

class RegularExpressionStringValidator extends StringValidator
{
    /**
     * @var string the regular expression to be matched with
     */
    public $pattern;

    /**
     * @var boolean whether to invert the validation logic. Defaults to false. If set to true,
     * the regular expression defined via [[pattern]] should NOT match the attribute value.
     */
    public $not = false;

    public $patternMessage;

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if ($res = parent::validateValue($value))
            return $res;

        if ($res = $this->validateValueByPattern($value))
            return $res;

        return null;
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        return parent::clientValidateAttribute($model, $attribute, $view)
            . $this->clientValidateAttributeByPattern($model, $attribute, $view);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->pattern === null) {
            throw new InvalidConfigException('The "pattern" property must be set.');
        }
        if ($this->patternMessage === null) {
            $this->patternMessage = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    private function validateValueByPattern($value)
    {
        $valid = !is_array($value) &&
            (!$this->not && preg_match($this->pattern, $value)
                || $this->not && !preg_match($this->pattern, $value));

        return $valid ? null : [$this->patternMessage, []];
    }

    private function clientValidateAttributeByPattern($model, $attribute, $view)
    {
        $pattern = Html::escapeJsRegularExpression($this->pattern);

        $options = [
            'pattern' => new JsExpression($pattern),
            'not' => $this->not,
            'message' => Yii::$app->getI18n()->format($this->patternMessage, [
                'attribute' => $model->getAttributeLabel($attribute),
            ], Yii::$app->language),
        ];
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        ValidationAsset::register($view);

        return 'yii.validation.regularExpression(value, messages, ' . Json::htmlEncode($options) . ');';
    }
}

