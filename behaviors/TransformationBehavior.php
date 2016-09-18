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

    private $_transformations;

    private $_transformers;

    /**
     * @var string[] Source attributes names ('transformed attribute name' => 'source attribute name')
     */
    private $_sourceAttributes;

    /**
     * Transformations getter.
     * When explicitly set to no null, return setted value. Otherwise call owner's transformations(),
     * and set property to result of execution.
     * Next, if owner hasn't transformations() method, return [];
     *
     * @return array
     */
    public function getTransformations()
    {
        if ($this->_transformations !== null)
            return $this->_transformations;

        if ($this->owner && $this->owner->hasMethod('transformations'))
            return $this->_transformations = $this->owner->transformations();

        return [];
    }

    /**
     * Transformations setter
     *
     * Transformation must be in format
     * ['property' (or ['property1' => 'transformed_property1', 'property2', ...]), 'transformer', 'param' => 'value', ...]
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

            foreach ($properties as $key => $value) {
                list(, $transformedAttribute) = $this->getAttributePair($key, $value);
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

            foreach ($properties as $key => $value) {
                list($property, $transformedName) = $this->getAttributePair($key, $value);
                $sourceAttributes[$transformedName] = $property;
            }
        }

        return $sourceAttributes;
    }

    /**
     * Helper function for parse attribute name declaration format.
     *
     * @param string $key If string, then source attribute name
     * @param string $value If $key is string - transformed attribute name, else source attribute name
     * @return string[] ['source_attribute_name', 'transformed_attribute_name']
     */
    private function getAttributePair($key, $value)
    {
        if (is_string($key))
            return [$key, $value];

        return [$value, $value . $this->suffix];
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