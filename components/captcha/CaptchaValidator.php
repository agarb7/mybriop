<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 06.05.15
 * Time: 0:24
 */

namespace app\components\captcha;

use Yii;
use yii\validators\ValidationAsset;


class CaptchaValidator extends \yii\captcha\CaptchaValidator
{
    public function clientValidateAttribute($object, $attribute, $view)
    {
        $captcha = $this->createCaptchaAction();
        $code = $captcha->getVerifyCode(false);
        $hash = $captcha->generateValidationHash($this->caseSensitive ? $code : mb_strtolower($code));
        $options = [
            'hash' => $hash,
            'hashKey' => 'yiiCaptcha/' . $this->captchaAction,
            'caseSensitive' => $this->caseSensitive,
            'message' => Yii::$app->getI18n()->format($this->message, [
                'attribute' => $object->getAttributeLabel($attribute),
            ], Yii::$app->language),
        ];
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        ValidationAsset::register($view);

        return 'yii.validation.captcha(value, messages, '
            . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ');';
    }
}