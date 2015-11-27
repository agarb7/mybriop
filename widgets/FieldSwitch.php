<?php
namespace app\widgets;

use yii\base\Widget;
use app\helpers\Html;

class FieldSwitch extends Widget
{
    public $model;
    public $fromAttribute;
    public $toAttribute;
    public $introText;
    public $linkText;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();

        return Html::beginTag('div', ['class'=>'field-switch', 'id' => $this->id])
        . $this->introText . ' '
        . Html::a($this->linkText, '#')
        . Html::endTag('div');
    }

    private function registerClientScript()
    {
        FieldSwitchAsset::register($this->view);

        $fromCont = $this->getFieldContainerClass($this->fromAttribute);
        $toCont = $this->getFieldContainerClass($this->toAttribute);

        $js = "jQuery('#$this->id').mybriopFieldSwitch({from:'.$fromCont',to:'.$toCont'});";

        $this->view->registerJs($js);
    }

    private function getFieldContainerClass($attribute)
    {
        return Html::getFieldContainerClass(Html::getInputId($this->model, $attribute));
    }
}
