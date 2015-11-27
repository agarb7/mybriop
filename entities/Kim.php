<?php
namespace app\entities;

/**
 * Kim record
 * @property string $text
 */
class Kim extends BaseMaterialKursa
{
    const TYPE_TEXT = 'text';

    public static function tableName()
    {
        return 'kim';
    }

    public function getType()
    {
        if ($this->text)
            return self::TYPE_TEXT;

        return parent::getType();
    }
}