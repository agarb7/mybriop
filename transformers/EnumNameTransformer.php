<?php
namespace app\transformers;

use yii\base\NotSupportedException;

class EnumNameTransformer extends EnumTransformerBase
{
    /**
     * @inheritdoc
     */
    protected function transformToValue($prop_value)
    {
        throw new NotSupportedException('Setting enum property from names is not supported.');
    }

    /**
     * @inheritdoc
     */
    protected function transformFromValue($col_value)
    {
        $class = $this->enum;
        return $class::getName($class::asValue($col_value));
    }
}