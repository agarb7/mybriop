<?php
namespace app\transformers;

class EnumTransformer extends EnumTransformerBase
{
    /**
     * @inheritdoc
     */
    protected function transformToValue($prop_value)
    {
        $class = $this->enum;
        return $class::asSql($prop_value);
    }

    /**
     * @inheritdoc
     */
    protected function transformFromValue($col_value)
    {
        $class = $this->enum;
        return $class::asValue($col_value);
    }
}