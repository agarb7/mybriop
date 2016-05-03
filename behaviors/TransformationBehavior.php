<?php
namespace app\behaviors;

use app\transformers2\Transformer;
use yii\base\Behavior;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class TransformationBehavior
 * Create virtual properties with transformed by [[Transformer]] values.
 * Useful for user input for attributes with format-restricted data types (e.g. date or phone number)
 */
class TransformationBehavior extends Behavior
{
    /**
     * Suffix that will be added to virtual property
     * @var string
     */
    public $suffix = '_input';

    private $_transformations = [];

    private $_transformers;

    /**
     * @var string[] Source attributes names ('transformed attribute name' => 'source attribute name')
     */
    private $_sourceAttributes;

    /**
     * Transformations getter
     *
     * @return array
     */
    public function getTransformations()
    {
        return $this->_transformations;
    }

    /**
     * Transformations setter
     *
     * Transformation must be in format
     * ['property' (or ['property1', 'property2', ...]), 'transformer', 'param' => 'value', ...]
     * 'transformer' must be:
     *  1) class-name of [[Transformer]];
     *  2) name of built-in transformer (e.g. 'date');
     *  3) instance of [[Transformer]]
     *
     * @param array $transformations array of transformations in described format
     */
    public function setTransformations($transformations)
    {
        $this->_transformations = $transformations;
        $this->_transformers = null;
        $this->_sourceAttributes = null;
    }

    /**
     * @return string[] Source attributes names ('transformed attribute name' => 'source attribute name')
     */
    public function getSourceAttributes()
    {
        if ($this->_sourceAttributes === null)
            $this->_sourceAttributes = $this->createSourceAttributes();

        return $this->_sourceAttributes;
    }

    /**
     * @param string $transformedAttribute Transformed attribute name
     * @return string
     */
    public function getSourceAttribute($transformedAttribute)
    {
        $sourceAttributes = $this->getSourceAttributes();

        return $sourceAttributes[$transformedAttribute];
    }

    /**
     * @param string $sourceAttribute Source attribute name
     * @return string Transformed attribute name
     */
    public function getTransformedAttribute($sourceAttribute)
    {
        return $sourceAttribute . $this->suffix;
    }

    /**
     * @inheritdoc
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return
            $this->transformedPropertyExists($name)
            || parent::canGetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return
            $this->transformedPropertyExists($name)
            || parent::canSetProperty($name, $checkVars);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if ($this->transformedPropertyExists($name))
            return $this->getTransformedProperty($name);

        return parent::__get($name);
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if ($this->transformedPropertyExists($name))
            $this->setTransformedProperty($name, $value);
        else
            parent::__set($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function __isset($name)
    {
        if ($this->transformedPropertyExists($name))
            return $this->getTransformedProperty($name) !== null;

        return parent::__isset($name);
    }

    private function getTransformers()
    {
        if ($this->_transformers === null)
            $this->_transformers = $this->createTransformers();

        return $this->_transformers;
    }

    private function createTransformers()
    {
        $transformers = [];

        foreach ($this->getTransformations() as $config) {
            $properties = ArrayHelper::remove($config, 0);
            if (!is_array($properties))
                $properties = [$properties];

            $type = ArrayHelper::remove($config, 1);

            foreach ($properties as $property) {
                $transformedAttribute = $this->getTransformedAttribute($property);
                $transformers[$transformedAttribute] = $type instanceof Transformer
                    ? $type
                    : Transformer::createTransformer($type, $config);
            }
        }

        return $transformers;
    }

    private function createSourceAttributes()
    {
        $sourceAttributes = [];

        foreach ($this->getTransformations() as $config) {
            $properties = ArrayHelper::remove($config, 0);
            if (!is_array($properties))
                $properties = [$properties];

            foreach ($properties as $property) {
                $transformedName = $this->getTransformedAttribute($property);
                $sourceAttributes[$transformedName] = $property;
            }
        }

        return $sourceAttributes;
    }

    private function transformedPropertyExists($name)
    {
        $transformers = $this->getTransformers();

        return isset($transformers[$name]);
    }

    /**
     * @param $name
     * @return Transformer
     */
    private function getTransformer($name)
    {
        $transformers = $this->getTransformers();

        return $transformers[$name];
    }

    private function setTransformedProperty($name, $value)
    {
        $sourceAttribute = $this->getSourceAttribute($name);
        $transformer = $this->getTransformer($name);

        $this->owner->$sourceAttribute = $transformer->backTransform($value);
    }

    private function getTransformedProperty($name)
    {
        $sourceAttribute = $this->getSourceAttribute($name);
        $transformer = $this->getTransformer($name);

        return $transformer->transform($this->owner->$sourceAttribute);
    }
}