<?php

namespace app\transformers;

class EnumArrayTransformer extends EnumTransformerBase
{
    /**
     * @inheritdoc
     */
    protected function transformToValue($prop_value)
    {
        $class = $this->enum;
        return $class::asSqlArray($prop_value);
    }

    /**
     * @inheritdoc
     */
    protected function transformFromValue($col_value)
    {
        $class = $this->enum;
        return $class::asValuesArray($col_value);
    }
}
