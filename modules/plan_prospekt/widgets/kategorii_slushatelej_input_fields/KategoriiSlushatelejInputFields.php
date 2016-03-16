<?php
namespace app\modules\plan_prospekt\widgets\kategorii_slushatelej_input_fields;

use app\modules\plan_prospekt\Asset;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

class KategoriiSlushatelejInputFields extends Widget
{
    /**
     * @var ActiveForm
     */
    public $form;

    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @inheritdoc
     */
    public function run()
    {
        Asset::register($this->view);

        $inputId = Html::getInputId($this->model, $this->attribute);

        $field = $this->render('_field', [
            'fieldClass' => 'field-' . $inputId,
            'inputId' => $inputId,
            'inputName' => Html::getInputName($this->model, $this->attribute) . '[]',
            'inputLabel' => $this->model->getAttributeLabel($this->attribute)
        ]);

        $options = Json::htmlEncode([
            'field' => $field,
            'firstAddTogglerText' => 'добавить свою категорию',
            'addTogglerText' => 'добавить ещё',
            'values' => $this->model->{$this->attribute}
        ]);

        $this->view->registerJs('jQuery("#' . $this->id . '").kategoriiSlushatelejInputFields(' . $options . ');');

        return Html::tag('div', $this->renderFirstToggler(), [
            'id' => $this->id,
            'class' => 'kategorii-slushatelej-input-fields'
        ]);
    }

    /**
     * @return string
     */
    protected function renderFirstToggler()
    {
        return Html::a('', '#', ['class' => 'add-toggler']);
    }
}
