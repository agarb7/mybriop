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
     * Transform value from source, preserve null
     * @param $value
     * @return mixed
     * @throws NotSupportedException
     */
    public function transform($value)
    {
        if ($value === null)
            return null;

        return $this->forward($value);
    }

    /**
     * Transform value to source, empty to null
     * @param $value
     * @return mixed
     * @throws NotSupportedException
     */
    public function backTransform($value)
    {
        if ($value === null || $value === '' || $value === [])
            return null;

        return $this->back($value);
    }

    /**
     * Transform value from source. Implements in derived
     *
     * @param mixed $value guaranteed not null
     * @return mixed
     * @throws NotSupportedException
     */
    protected function forward($value)
    {
        throw new NotSupportedException('Direct transformation isn\'t available');
    }

    /**
     * Transform value to source. Implements in derived
     *
     * @param mixed $value guaranteed not null
     * @return mixed
     * @throws NotSupportedException
     */
    protected function back($value)
    {
        throw new NotSupportedException('Back transformation isn\'t available');
    }
}