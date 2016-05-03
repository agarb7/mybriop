<?php
namespace app\transformers2;

use yii\base\Component;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use Yii;

class Transformer extends Component
{
    /**
     * @var array list of built-in transformers (name => class or configuration)
     */
    public static $builtInTransformers = [
        'date' => 'app\transformers2\DateTransformer',
    ];

    public static function createTransformer($type, $config)
    {
        $config['class'] = ArrayHelper::getValue(static::$builtInTransformers, $type, $type);

        return Yii::createObject($config);
    }

    /**
     * Transform value from source
     * @param $value
     * @return mixed
     * @throws NotSupportedException
     */
    public function transform($value)
    {
        throw new NotSupportedException('Direct transformation isn\'t available');
    }

    /**
     * Transform value to source
     * @param $value
     * @return mixed
     * @throws NotSupportedException
     */
    public function backTransform($value)
    {
        throw new NotSupportedException('Back transformation isn\'t available');
    }
}