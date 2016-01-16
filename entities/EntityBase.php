<?php
namespace app\entities;

use app\transformers\Transformer;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\UnknownPropertyException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use Yii;

class EntityBase extends ActiveRecord
{
    public function __construct($config = [])
    {
        $this->_transformers = static::createTransformers();

        parent::__construct($config);
    }

    public function getHashids()
    {
        return Yii::$app->hashids->encode($this->primaryKey);
    }

    public static function findOneByHashids($hashids)
    {
        return static::findOne(Yii::$app->hashids->decode($hashids));
    }

    /**
     * @inheritdoc
     * @return EntityQuery the newly created [[EntityQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(EntityQuery::className(), [get_called_class()]);
    }

    /**
     * @return array
     * Item can be:
     *  - ['transName' => 'column_name', 'class_name', [params]]
     *  - ['transName' => 'column_name', $inline_transformer_config]
     *  - Transformer
     */
    public function transformations()
    {
        return [];
    }

    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (UnknownPropertyException $e) {
            if (isset($this->_transformers[$name]))
                return $this->_transformers[$name]->transformFrom();

            return parent::__get(static::prop2col($name));
        }
    }

    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (UnknownPropertyException $e) {
            if (isset($this->_transformers[$name]))
                $this->_transformers[$name]->transformTo($value);
            else
                parent::__set(static::prop2col($name), $value);
        }
    }

    public function __isset($name)
    {
        if (parent::__isset($name))
            return true;

        if (isset($this->_transformers[$name]))
            return true;

        if (parent::__isset(static::prop2col($name)))
            return true;

        return false;
    }

    public function __unset($name)
    {
        try {
            parent::__unset($name);
        } catch (InvalidCallException $e) {
            if (isset($this->_transformers[$name]))
                unset($this->_transformers[$name]);
            else
                parent::__unset(static::prop2col($name)); //todo: May be change this behaviour?
        }
    }

    protected static function prop2col($prop)
    {
        return Inflector::camel2id($prop, '_');
    }

    /**
     * @return Transformer[]
     * @throws InvalidConfigException
     */
    public function createTransformers()
    {
        $transformers = [];

        foreach ($this->transformations() as $transformation) {
            if ($transformation instanceof Transformer) {
                $transformers[$transformation->property] = $transformation;
            } elseif (is_array($transformation)) {
                $transformer = self::createTransformer($transformation);
                $transformers[$transformer->property] = $transformer;
            } else {
                throw new InvalidConfigException('Data transformations is invalid. It must be array or Transformer object.');
            }
        }

        return $transformers;
    }

    /**
     * @param $transformation
     * @return Transformer
     * @throws InvalidConfigException
     * @see transformations()
     */
    private function createTransformer($transformation)
    {
        $config = [];

        reset($transformation);

        list($config['property'], $config['column']) = each($transformation);
        if (!is_string($config['property']) || !is_string($config['column']))
            throw new InvalidConfigException('Data transformations is invalid. First array item must be "prop" => "col" pair');

        list(,$val2) = each($transformation);

        if (is_string($val2)) {
            $config['class'] = $val2;

            list(,$params) = each($transformation);
            if (is_array($params))
                $config = ArrayHelper::merge($config, $params);
            elseif ($params !== null)
                throw new InvalidConfigException('Data transformations is invalid. If params is presented it must be array');

        } elseif (is_array($val2)) {
            $config['class'] = InlineTransformer::className();

            $config = ArrayHelper::merge($config, $val2);
        }

        $config['model'] = $this;

        return \Yii::createObject($config);
    }

    /**
     * @var Transformer[]
     */
    private $_transformers;
}