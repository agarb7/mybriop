<?php
namespace app\transformers;

use yii\base\Component;

class Transformer extends Component
{
    public $model;

    public $property;
    public $column;

    /**
     * Set to column property value: transformed $value.
     * Default behaviour is to set value directly
     */
    public function transformTo($prop_value)
    {
        $column = $this->column;
        $this->model->$column = $this->transformToValue($prop_value);
    }

    /**
     * Return property value: transformed value from column
     * Default behaviour is to return value directly
     */
    public function transformFrom()
    {
        $column = $this->column;
        return $this->transformFromValue($this->model->$column);
    }

    protected function transformToValue($prop_value)
    {
        return $prop_value;
    }

    protected function transformFromValue($col_value)
    {
        return $col_value;
    }
}