<?php
namespace app\transformers;

use app\helpers\StringHelper;

class PasportKemVydanKodTransformer extends Transformer
{
    /**
     * @inheritdoc
     */
    protected function transformToValue($prop_value)
    {
        if ($prop_value === null)
            return null;

        return StringHelper::onlyDigits($prop_value);
    }

    /**
     * @inheritdoc
     */
    protected function transformFromValue($col_value)
    {
        if ($col_value === null)
            return null;

        return substr($col_value, 0, 3) . '-' . substr($col_value, 3, 3);
    }
}
