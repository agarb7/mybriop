<?php
namespace app\helpers;

use Yii;

class Html extends \yii\helpers\Html
{
    /**
     * @param string $inputID html id attribute of input
     * @return string field container css class that used by [[ActiveField]].
     */
    public static function getFieldContainerClass($inputID)
    {
        return "field-$inputID";
    }

    public static function returningA($text, $options = [])
    {
        return parent::a($text, Yii::$app->getUser()->getReturnUrl(), $options);
    }
}
