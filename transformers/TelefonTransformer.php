<?php
namespace app\transformers;

/**
 * Class TelefonTransformer
 * Telefon is stored in DB as 10 digit. TelefonTransformer make values like '+79141234567' from '9141234567', and vice versa.
 * @package app\transformers
 */
class TelefonTransformer extends Transformer
{
    /**
     * @inheritdoc
     */
    protected function transformToValue($prop_value)
    {
        if ($prop_value === null)
            return null;

        $res = preg_replace('/^\s*\+7/u', '', $prop_value);
        return preg_replace('/\D/u', '', $res);
    }

    /**
     * @inheritdoc
     */
    protected function transformFromValue($col_value)
    {
        if ($col_value === null)
            return null;

        return '+7' . $col_value;
    }
}